<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;

        // Log de depuración al crear el evento
        Log::channel('broadcasting')->info('NewMessageEvent creado', [
            'message_id' => $message->id,
            'conversation_id' => $message->conversation_id,
            'user_id' => $message->user_id
        ]);
    }

    public function broadcastOn(): array
    {
        $conversation = $this->message->conversation;
        $recipientId = $conversation->user1_id === $this->message->user_id
            ? $conversation->user2_id
            : $conversation->user1_id;

        // Log de depuración
        \Log::channel('broadcasting')->debug('Broadcasting canal', [
            'canal' => "user.{$recipientId}",
            'mensaje_id' => $this->message->id,
            'conversacion_id' => $conversation->id,
            'remitente_id' => $this->message->user_id,
            'receptor_id' => $recipientId
        ]);

        return [
            new PrivateChannel('user.' . $recipientId),
        ];
    }

    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'content' => $this->message->content,
                'user_id' => $this->message->user_id,
                'user_name' => $this->message->user->name,
                'created_at' => $this->message->created_at,
            ]
        ];
    }

}
