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

class NewMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
        // Asegurarnos de cargar la relación usuario
        $this->message->load('user');
    }

    public function broadcastOn(): array
    {
        $conversation = $this->message->conversation;
        $recipientId = $conversation->user1_id === $this->message->user_id
            ? $conversation->user2_id
            : $conversation->user1_id;

        return [
            new PrivateChannel('user.' . $recipientId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'conversation_id' => $this->message->conversation_id,
                'content' => $this->message->content,
                'user_id' => $this->message->user_id,
                'user_name' => $this->message->user->name,
                'created_at' => $this->message->created_at,
                'read' => $this->message->read,
            ]
        ];
    }
}
