<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewNotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;

        // Log de depuración
        Log::channel('broadcasting')->info('NewNotificationEvent creado', [
            'notification_id' => $notification->id,
            'user_id' => $notification->user_id
        ]);
    }

    public function broadcastOn(): array
    {
        // Transmitir al canal privado del usuario
        return [
            new PrivateChannel('user.' . $this->notification->user_id),
        ];
    }

    public function broadcastWith()
    {
        return [
            'notification' => [
                'id' => $this->notification->id,
                'type' => $this->notification->type,
                'message' => $this->notification->message,
                'link' => $this->notification->link,
                'related_id' => $this->notification->related_id,
                'related_type' => $this->notification->related_type,
                'created_at' => $this->notification->created_at,
            ]
        ];
    }

    // Método para manejar errores de broadcasting
    public function broadcastException(\Throwable $exception)
    {
        Log::channel('broadcasting')->error('Broadcasting error en notificación', [
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
