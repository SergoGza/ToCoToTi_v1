<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Canal privado para mensajería entre usuarios
Broadcast::channel('user.{id}', function ($user, $id) {
    \Log::info("Canal solicitado: user.{$id}, Usuario autenticado: " . ($user ? $user->id : 'No autenticado'));
    return (int) $user->id === (int) $id;
});
