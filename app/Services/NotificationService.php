<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\ItemInterest;
use App\Models\Item;
use App\Models\User;

class NotificationService
{
    /**
     * Crear notificación cuando se recibe un interés en un ítem
     */
    public static function createInterestReceivedNotification(ItemInterest $interest)
    {
        $item = $interest->item;
        $interestedUser = $interest->user;

        return Notification::create([
            'user_id' => $item->user_id,
            'type' => 'interest_received',
            'message' => "{$interestedUser->name} está interesado en tu ítem '{$item->title}'",
            'link' => route('interests.received'),
            'related_id' => $interest->id,
            'related_type' => 'interest'
        ]);
    }

    /**
     * Crear notificación cuando un interés es aceptado
     */
    public static function createInterestAcceptedNotification(ItemInterest $interest)
    {
        $item = $interest->item;

        return Notification::create([
            'user_id' => $interest->user_id,
            'type' => 'interest_accepted',
            'message' => "Tu interés en el ítem '{$item->title}' ha sido aceptado",
            'link' => route('items.show', $item->id),
            'related_id' => $interest->id,
            'related_type' => 'interest'
        ]);
    }

    /**
     * Crear notificación cuando cambia el estado de un interés
     */
    public static function createInterestStatusNotification(ItemInterest $interest, $oldStatus)
    {
        $item = $interest->item;
        $message = '';

        if ($interest->status === 'accepted') {
            $message = "Tu interés en el ítem '{$item->title}' ha sido aceptado";
        } elseif ($interest->status === 'rejected') {
            $message = "Tu interés en el ítem '{$item->title}' ha sido rechazado";
        }

        if ($message) {
            return Notification::create([
                'user_id' => $interest->user_id,
                'type' => 'interest_' . $interest->status,
                'message' => $message,
                'link' => route('interests.index'),
                'related_id' => $interest->id,
                'related_type' => 'interest'
            ]);
        }
    }

    /**
     * Crear notificación cuando un ítem es reservado
     */
    public static function createItemReservedNotification(Item $item)
    {
        // Notificar a todos los usuarios interesados que no fueron seleccionados
        $otherInterests = $item->interests()
            ->where('status', '!=', 'accepted')
            ->get();

        foreach ($otherInterests as $interest) {
            Notification::create([
                'user_id' => $interest->user_id,
                'type' => 'item_reserved',
                'message' => "El ítem '{$item->title}' ha sido reservado para otro usuario",
                'link' => route('interests.index'),
                'related_id' => $item->id,
                'related_type' => 'item'
            ]);
        }
    }

    /**
     * Crear notificación cuando cambia el estado de un interés
     */
    public static function createInterestStatusChangedNotification(ItemInterest $interest, $oldStatus, $newStatus)
    {
        $item = $interest->item;
        $messages = [
            'accepted' => "Tu interés en el ítem '{$item->title}' ha sido aceptado",
            'rejected' => "Tu interés en el ítem '{$item->title}' ha sido rechazado",
            'pending' => "El ítem '{$item->title}' vuelve a estar disponible"
        ];

        if (isset($messages[$newStatus])) {
            return Notification::create([
                'user_id' => $interest->user_id,
                'type' => 'interest_' . $newStatus,
                'message' => $messages[$newStatus],
                'link' => route('interests.index'),
                'related_id' => $interest->id,
                'related_type' => 'interest'
            ]);
        }
    }

    /**
     * Notificar a otros usuarios cuando un ítem es reservado
     */
    public static function createItemReservedForOthersNotification(Item $item, $selectedUserId)
    {
        // Notificar a todos los usuarios interesados excepto al seleccionado
        $otherInterests = $item->interests()
            ->where('user_id', '!=', $selectedUserId)
            ->where('status', 'pending')
            ->get();

        foreach ($otherInterests as $interest) {
            Notification::create([
                'user_id' => $interest->user_id,
                'type' => 'item_reserved',
                'message' => "El ítem '{$item->title}' ha sido reservado para otro usuario",
                'link' => route('interests.index'),
                'related_id' => $item->id,
                'related_type' => 'item'
            ]);
        }
    }

    /**
     * Crear notificación cuando un ítem es entregado
     */
    public static function createItemGivenNotification(Item $item, $recipientUserId)
    {
        return Notification::create([
            'user_id' => $recipientUserId,
            'type' => 'item_given',
            'message' => "El ítem '{$item->title}' te ha sido entregado. ¡Disfrútalo!",
            'link' => route('interests.index'),
            'related_id' => $item->id,
            'related_type' => 'item'
        ]);
    }
}
