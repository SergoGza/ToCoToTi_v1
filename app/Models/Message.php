<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'read'
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    /**
     * Obtener la conversación a la que pertenece este mensaje
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Obtener el usuario que envió el mensaje
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Marcar el mensaje como leído
     */
    public function markAsRead()
    {
        if (!$this->read) {
            $this->read = true;
            $this->save();
        }
    }
}
