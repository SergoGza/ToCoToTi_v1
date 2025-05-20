<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user1_id',
        'user2_id',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Obtener el primer usuario de la conversación
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Obtener el segundo usuario de la conversación
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Obtener todos los mensajes de la conversación
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Obtener el mensaje más reciente de la conversación
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    /**
     * Obtener los mensajes no leídos para un usuario específico
     */
    public function unreadMessages($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('read', false);
    }

    /**
     * Obtener el otro usuario de la conversación (no el actual)
     */
    public function otherUser($userId)
    {
        return $this->user1_id == $userId ? $this->user2 : $this->user1;
    }
}
