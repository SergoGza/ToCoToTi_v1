<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function interests()
    {
        return $this->hasMany(ItemInterest::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotificationsCount()
    {
        return $this->notifications()->where('read', false)->count();
    }

    /**
     * Mensajes enviados por este usuario.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Mensajes recibidos por este usuario.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Contar mensajes no leídos.
     */
    public function unreadMessagesCount()
    {
        return $this->receivedMessages()->where('read', false)->count();
    }

    /**
     * Obtener conversaciones (usuarios con los que se ha intercambiado mensajes).
     */
    public function conversations()
    {
        $sentToUserIds = $this->sentMessages()
            ->select('receiver_id')
            ->distinct()
            ->pluck('receiver_id');

        $receivedFromUserIds = $this->receivedMessages()
            ->select('sender_id')
            ->distinct()
            ->pluck('sender_id');

        $userIds = $sentToUserIds->merge($receivedFromUserIds)->unique();

        return User::whereIn('id', $userIds)->get();
    }
}
