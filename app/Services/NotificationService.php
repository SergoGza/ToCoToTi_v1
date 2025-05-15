<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\ItemInterest;
use App\Models\Item;
use App\Models\User;
use App\Models\Message;

class NotificationService
{
    // Mantener todos los métodos anteriores...

    /**
     * Crear notificación cuando se recibe un mensaje.
     */
    public static function createMessageReceivedNotification(Message $message)
    {
        // Título del ítem si existe
        $itemTitle = '';
        if ($message->item_id) {
            $itemTitle = " sobre el ítem '{$message->item->title}'";
        } elseif ($message->item_interest_id && $message->interest->item) {
            $itemTitle = " sobre el ítem '{$message->interest->item->title}'";
        }

        Notification::create([
            'user_id' => $message->receiver_id,
            'type' => 'message_received',
            'message' => "Has recibido un mensaje de {$message->sender->name}{$itemTitle}",
            'link' => route('messages.show', $message->sender_id),
            'related_id' => $message->id,
            'related_type' => 'message',
            'read' => false
        ]);
    }
}
