<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info('🌱 Iniciando el sembrado de la base de datos...');


        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ItemSeeder::class,
            RequestSeeder::class,
            ItemInterestSeeder::class,
        ]);


        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Base de datos sembrada exitosamente');
        $this->command->info('');
        $this->command->info('📊 Datos creados:');
        $this->command->info('👥 Usuarios: ' . \App\Models\User::count());
        $this->command->info('📦 Categorías: ' . \App\Models\Category::count());
        $this->command->info('🎁 Items: ' . \App\Models\Item::count());
        $this->command->info('📋 Solicitudes: ' . \App\Models\Request::count());
        $this->command->info('❤️ Intereses: ' . \App\Models\ItemInterest::count());
        $this->command->info('');
        $this->command->info('🔐 Usuario de prueba:');
        $this->command->info('   Email: admin@tocototi.com');
        $this->command->info('   Password: password');
    }
}
