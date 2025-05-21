<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\NewNotificationEvent;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'link',
        'related_id',
        'related_type',
        'read'
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model based on related_type.
     */
    public function related()
    {
        if ($this->related_type === 'item') {
            return $this->belongsTo(Item::class, 'related_id');
        } elseif ($this->related_type === 'interest') {
            return $this->belongsTo(ItemInterest::class, 'related_id');
        } elseif ($this->related_type === 'request') {
            return $this->belongsTo(Request::class, 'related_id');
        }

        return null;
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::created(function ($notification) {
            // Disparar evento para WebSocket cuando se crea una notificación
            broadcast(new NewNotificationEvent($notification));
        });
    }
}
