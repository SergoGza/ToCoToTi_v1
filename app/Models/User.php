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
        'welcome_tour_completed',
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
            'welcome_tour_completed' => 'boolean',
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
        return $this->hasMany(Message::class, 'user_id');
    }

    /**
     * Conversaciones donde este usuario participa.
     */
    public function conversations()
    {
        return Conversation::where('user1_id', $this->id)
            ->orWhere('user2_id', $this->id);
    }

    /**
     * Contar mensajes no leídos.
     */
    public function unreadMessagesCount()
    {
        $conversations = $this->conversations()->get();
        $count = 0;

        foreach ($conversations as $conversation) {
            $count += $conversation->unreadMessages($this->id)->count();
        }

        return $count;
    }

    /**
     * Obtener conversaciones (usuarios con los que se ha intercambiado mensajes).
     */
    public function getConversationWith($userId)
    {
        return Conversation::where(function ($query) use ($userId) {
            $query->where('user1_id', $this->id)
                ->where('user2_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user1_id', $userId)
                ->where('user2_id', $this->id);
        })->first();
    }

    /**
     * Marcar el tour de bienvenida como completado
     */
    public function markWelcomeTourCompleted()
    {
        $this->update(['welcome_tour_completed' => true]);
    }

    /**
     * Verificar si el usuario ha completado el tour de bienvenida
     */
    public function hasCompletedWelcomeTour()
    {
        return $this->welcome_tour_completed;
    }
}
