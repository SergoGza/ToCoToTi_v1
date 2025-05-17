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

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;

        // Cargar relaciones necesarias para la serialización
        // Esto es crucial para que el evento contenga toda la información necesaria
        $this->message->load(['sender', 'receiver', 'item', 'interest']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Enviamos el mensaje a un canal para cada participante de la conversación
        // Esto asegura que ambos usuarios reciban la actualización
        return [
            new PrivateChannel("chat.{$this->message->receiver_id}"),
            new PrivateChannel("chat.{$this->message->sender_id}")
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new.message';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        // Formatear los datos para que sean más fáciles de usar en el frontend
        return [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'content' => $this->message->content,
            'item_id' => $this->message->item_id,
            'item_interest_id' => $this->message->item_interest_id,
            'images' => $this->message->images,
            'read' => $this->message->read,
            'created_at' => $this->message->created_at,
            'sender' => [
                'id' => $this->message->sender->id,
                'name' => $this->message->sender->name
            ],
            'receiver' => [
                'id' => $this->message->receiver->id,
                'name' => $this->message->receiver->name
            ],
            'item' => $this->message->item ? [
                'id' => $this->message->item->id,
                'title' => $this->message->item->title
            ] : null,
            'interest' => $this->message->interest ? [
                'id' => $this->message->interest->id
            ] : null
        ];
    }
}
