<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'item_id',
        'item_interest_id',
        'content',
        'images',
        'read',
        'read_at'
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
        'images' => 'array',
    ];

    /**
     * Obtener el remitente del mensaje.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Obtener el destinatario del mensaje.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Obtener el ítem relacionado con el mensaje.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Obtener el interés relacionado con el mensaje.
     */
    public function interest()
    {
        return $this->belongsTo(ItemInterest::class, 'item_interest_id');
    }
}
