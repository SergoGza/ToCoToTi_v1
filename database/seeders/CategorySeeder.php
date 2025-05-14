<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electrodomésticos', 'description' => 'Refrigeradores, lavadoras, microondas, etc.', 'icon' => 'home-appliance'],
            ['name' => 'Muebles', 'description' => 'Sofás, mesas, sillas, armarios, etc.', 'icon' => 'furniture'],
            ['name' => 'Ropa', 'description' => 'Prendas de vestir y accesorios', 'icon' => 'clothing'],
            ['name' => 'Electrónica', 'description' => 'Teléfonos, tablets, computadoras, etc.', 'icon' => 'electronics'],
            ['name' => 'Libros', 'description' => 'Libros, revistas y material de lectura', 'icon' => 'book'],
            ['name' => 'Juguetes', 'description' => 'Juegos y juguetes para niños', 'icon' => 'toy'],
            ['name' => 'Herramientas', 'description' => 'Herramientas de trabajo y bricolaje', 'icon' => 'tool'],
            ['name' => 'Deportes', 'description' => 'Equipamiento y artículos deportivos', 'icon' => 'sports'],
            ['name' => 'Otros', 'description' => 'Artículos que no encajan en otras categorías', 'icon' => 'other']
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
