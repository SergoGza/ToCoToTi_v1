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

    public $messageData;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        // En lugar de guardar el modelo completo, extraemos solo los datos necesarios
        // para evitar problemas de serialización con relaciones cíclicas
        $this->messageData = [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'content' => $message->content,
            'item_id' => $message->item_id,
            'item_interest_id' => $message->item_interest_id,
            'images' => $message->images,
            'read' => $message->read,
            'created_at' => $message->created_at,
            'sender' => $message->sender ? [
                'id' => $message->sender->id,
                'name' => $message->sender->name
            ] : null,
            'receiver' => $message->receiver ? [
                'id' => $message->receiver->id,
                'name' => $message->receiver->name
            ] : null,
            'item' => $message->item ? [
                'id' => $message->item->id,
                'title' => $message->item->title
            ] : null,
            'interest' => $message->interest ? [
                'id' => $message->interest->id
            ] : null
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("chat.{$this->messageData['receiver_id']}"),
            new PrivateChannel("chat.{$this->messageData['sender_id']}")
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
     */
    public function broadcastWith(): array
    {
        return $this->messageData;
    }
}
