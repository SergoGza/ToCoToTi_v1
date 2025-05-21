<?php

namespace App\Services;

use App\Models\User;
use App\Models\Item;
use App\Models\ItemInterest;
use App\Models\Notification;
use App\Models\Request as ItemRequest;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Crear una notificación cuando alguien muestra interés en un ítem.
     */
    public static function createInterestReceivedNotification(ItemInterest $interest)
    {
        try {
            // Obtener el propietario del ítem
            $itemOwner = $interest->item->user;

            // Crear la notificación
            Notification::create([
                'user_id' => $itemOwner->id,
                'type' => 'interest_received',
                'message' => "{$interest->user->name} ha mostrado interés en tu ítem \"{$interest->item->title}\"",
                'link' => route('items.show', $interest->item_id),
                'related_id' => $interest->id,
                'related_type' => 'interest',
                'read' => false
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al crear notificación de interés recibido', [
                'error' => $e->getMessage(),
                'interest_id' => $interest->id
            ]);

            return false;
        }
    }

    /**
     * Crear una notificación cuando se acepta un interés.
     */
    public static function createInterestAcceptedNotification(ItemInterest $interest)
    {
        try {
            // Crear la notificación para el usuario interesado
            Notification::create([
                'user_id' => $interest->user_id,
                'type' => 'interest_accepted',
                'message' => "Tu interés en el ítem \"{$interest->item->title}\" ha sido aceptado",
                'link' => route('items.show', $interest->item_id),
                'related_id' => $interest->id,
                'related_type' => 'interest',
                'read' => false
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al crear notificación de interés aceptado', [
                'error' => $e->getMessage(),
                'interest_id' => $interest->id
            ]);

            return false;
        }
    }

    /**
     * Crear una notificación cuando se rechaza un interés.
     */
    public static function createInterestRejectedNotification(ItemInterest $interest)
    {
        try {
            // Crear la notificación para el usuario interesado
            Notification::create([
                'user_id' => $interest->user_id,
                'type' => 'interest_rejected',
                'message' => "Tu interés en el ítem \"{$interest->item->title}\" ha sido rechazado",
                'link' => route('items.show', $interest->item_id),
                'related_id' => $interest->id,
                'related_type' => 'interest',
                'read' => false
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al crear notificación de interés rechazado', [
                'error' => $e->getMessage(),
                'interest_id' => $interest->id
            ]);

            return false;
        }
    }

    /**
     * Crear una notificación genérica sobre cambio de estado de un interés.
     */
    public static function createInterestStatusNotification(ItemInterest $interest, $oldStatus)
    {
        if ($interest->status === 'accepted') {
            return self::createInterestAcceptedNotification($interest);
        } else if ($interest->status === 'rejected') {
            return self::createInterestRejectedNotification($interest);
        }

        return false;
    }

    /**
     * Notificar a otros usuarios interesados que el ítem ha sido reservado.
     */
    public static function createItemReservedNotification(Item $item)
    {
        try {
            // Obtener todos los intereses pendientes
            $pendingInterests = $item->interests()
                ->where('status', 'pending')
                ->get();

            foreach ($pendingInterests as $interest) {
                Notification::create([
                    'user_id' => $interest->user_id,
                    'type' => 'item_reserved',
                    'message' => "El ítem \"{$item->title}\" en el que estabas interesado ha sido reservado",
                    'link' => route('items.show', $item->id),
                    'related_id' => $item->id,
                    'related_type' => 'item',
                    'read' => false
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error al crear notificaciones de ítem reservado', [
                'error' => $e->getMessage(),
                'item_id' => $item->id
            ]);

            return false;
        }
    }

    /**
     * Notificar a un usuario específico que el ítem ha sido reservado para otros.
     */
    public static function createItemReservedForOthersNotification(Item $item, $exceptUserId)
    {
        try {
            // Obtener todos los intereses pendientes excepto el del usuario exceptuado
            $otherInterests = $item->interests()
                ->where('status', 'pending')
                ->where('user_id', '!=', $exceptUserId)
                ->get();

            foreach ($otherInterests as $interest) {
                Notification::create([
                    'user_id' => $interest->user_id,
                    'type' => 'item_reserved_others',
                    'message' => "El ítem \"{$item->title}\" en el que estabas interesado ha sido reservado para otro usuario",
                    'link' => route('items.show', $item->id),
                    'related_id' => $item->id,
                    'related_type' => 'item',
                    'read' => false
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error al crear notificaciones de ítem reservado para otros', [
                'error' => $e->getMessage(),
                'item_id' => $item->id
            ]);

            return false;
        }
    }

    /**
     * Notificar que un ítem ha sido entregado.
     */
    public static function createItemGivenNotification(Item $item, $recipientUserId)
    {
        try {
            Notification::create([
                'user_id' => $recipientUserId,
                'type' => 'item_given',
                'message' => "El ítem \"{$item->title}\" te ha sido entregado",
                'link' => route('items.show', $item->id),
                'related_id' => $item->id,
                'related_type' => 'item',
                'read' => false
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al crear notificación de ítem entregado', [
                'error' => $e->getMessage(),
                'item_id' => $item->id
            ]);

            return false;
        }
    }

    /**
     * Crear notificación cuando se encuentran ítems que coinciden con una solicitud.
     */
    public static function createMatchingItemsNotification(ItemRequest $request, $itemsCount)
    {
        try {
            Notification::create([
                'user_id' => $request->user_id,
                'type' => 'matching_items',
                'message' => "Encontramos {$itemsCount} " . ($itemsCount == 1 ? 'ítem' : 'ítems') . " para tu solicitud \"{$request->title}\"",
                'link' => route('requests.show', $request->id),
                'related_id' => $request->id,
                'related_type' => 'request',
                'read' => false
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al crear notificación de ítems coincidentes', [
                'error' => $e->getMessage(),
                'request_id' => $request->id
            ]);

            return false;
        }
    }
}
