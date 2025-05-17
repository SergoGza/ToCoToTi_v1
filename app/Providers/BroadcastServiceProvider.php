<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Usar configuración básica sin middleware adicional para pruebas
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}
