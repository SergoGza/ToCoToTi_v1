<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario administrador/test principal
        User::create([
            'name' => 'Admin ToCoToTi',
            'email' => 'admin@tocototi.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'welcome_tour_completed' => true,
        ]);

        // Usuario de prueba común
        User::create([
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'welcome_tour_completed' => true,
        ]);

        User::create([
            'name' => 'Carlos Rodríguez',
            'email' => 'carlos@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'welcome_tour_completed' => false,
        ]);

        User::create([
            'name' => 'Ana López',
            'email' => 'ana@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'welcome_tour_completed' => true,
        ]);

        User::create([
            'name' => 'Pedro Martínez',
            'email' => 'pedro@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'welcome_tour_completed' => true,
        ]);

        // Usuarios adicionales usando Factory
        User::factory(15)->create();
    }
}
