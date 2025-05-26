<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        // Items específicos con datos realistas
        $specificItems = [
            [
                'title' => 'Sofá cama en muy buen estado',
                'description' => 'Sofá cama de dos plazas, color gris oscuro. Se convierte fácilmente en cama matrimonial. Muy cómodo y en excelente estado. Lo regalo porque me mudo a un apartamento más pequeño.',
                'condition' => 'buen_estado',
                'status' => 'available',
                'location' => 'Madrid Centro',
                'category_id' => $categories->where('name', 'Muebles')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Microondas Samsung 20L',
                'description' => 'Microondas Samsung de 20 litros, color blanco. Funciona perfectamente, solo tiene algunos rayones menores en la superficie. Incluye plato giratorio.',
                'condition' => 'usado',
                'status' => 'available',
                'location' => 'Barcelona, Eixample',
                'category_id' => $categories->where('name', 'Electrodomésticos')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Colección de libros de cocina',
                'description' => 'Lote de 8 libros de cocina de diferentes autores. Incluye recetas mediterráneas, postres y cocina internacional. Están en muy buen estado.',
                'condition' => 'buen_estado',
                'status' => 'reserved',
                'location' => 'Valencia',
                'category_id' => $categories->where('name', 'Libros')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Bicicleta de montaña',
                'description' => 'Bicicleta de montaña marca Trek, 21 velocidades. Necesita ajuste de frenos y cadena, pero es perfecta para alguien que la quiera restaurar.',
                'condition' => 'necesita_reparacion',
                'status' => 'available',
                'location' => 'Sevilla Norte',
                'category_id' => $categories->where('name', 'Deportes')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Smartphone Android (para repuestos)',
                'description' => 'Teléfono móvil que ya no enciende. Pantalla en buen estado, puede servir para repuestos o para alguien que sepa repararlo.',
                'condition' => 'necesita_reparacion',
                'status' => 'available',
                'location' => 'Bilbao',
                'category_id' => $categories->where('name', 'Electrónica')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Juegos de mesa familiares',
                'description' => 'Lote de juegos de mesa: Monopoly, Scrabble, Parchís y Trivial. Todos completos y en buen estado. Perfectos para tardes en familia.',
                'condition' => 'buen_estado',
                'status' => 'given',
                'location' => 'Zaragoza',
                'category_id' => $categories->where('name', 'Juguetes')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Ropa de bebé (0-12 meses)',
                'description' => 'Lote de ropa de bebé unisex, tallas de 0 a 12 meses. Incluye bodies, pijamas, pantalones y camisetas. Todo limpio y en buen estado.',
                'condition' => 'buen_estado',
                'status' => 'available',
                'location' => 'Málaga',
                'category_id' => $categories->where('name', 'Ropa')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Taladro eléctrico con accesorios',
                'description' => 'Taladro eléctrico Bosch con maletín de brocas y accesorios. Funciona perfectamente, lo regalo porque ya no hago bricolaje.',
                'condition' => 'buen_estado',
                'status' => 'available',
                'location' => 'Palencia Centro',
                'category_id' => $categories->where('name', 'Herramientas')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Mesa de escritorio IKEA',
                'description' => 'Mesa de escritorio de IKEA, color blanco, 120x60cm. Está en muy buen estado, solo pequeñas marcas de uso normal. Ideal para estudiar o trabajar.',
                'condition' => 'buen_estado',
                'status' => 'available',
                'location' => 'Madrid Sur',
                'category_id' => $categories->where('name', 'Muebles')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Cafetera espresso manual',
                'description' => 'Cafetera espresso manual italiana, aluminio. Hace un café delicioso, la regalo porque me han regalado una automática.',
                'condition' => 'como_nuevo',
                'status' => 'available',
                'location' => 'Barcelona, Gràcia',
                'category_id' => $categories->where('name', 'Electrodomésticos')->first()?->id,
                'user_id' => $users->random()->id,
            ],
        ];

        // Crear items específicos
        foreach ($specificItems as $itemData) {
            Item::create($itemData);
        }

        // Crear items adicionales aleatorios
        for ($i = 0; $i < 25; $i++) {
            Item::create([
                'title' => $this->generateRandomTitle(),
                'description' => $this->generateRandomDescription(),
                'condition' => $this->getRandomCondition(),
                'status' => $this->getRandomStatus(),
                'location' => $this->getRandomLocation(),
                'category_id' => $categories->random()->id,
                'user_id' => $users->random()->id,
            ]);
        }
    }

    private function generateRandomTitle(): string
    {
        $items = [
            'Lámpara de pie', 'Silla de oficina', 'Cojines decorativos', 'Espejo antiguo',
            'Macetas de barro', 'Libros universitarios', 'DVD películas', 'Ropa vintage',
            'Zapatos deportivos', 'Mochila escolar', 'Ordenador portátil', 'Tablet Android',
            'Cargadores varios', 'Cables HDMI', 'Juguetes educativos', 'Peluches',
            'Juegos PlayStation', 'Consola retro', 'Herramientas jardín', 'Taladro percutor',
            'Martillo y destornilladores', 'Sierra manual', 'Raqueta tenis', 'Balón fútbol',
            'Pesas gimnasio', 'Cinta correr', 'Plancha vapor', 'Aspiradora', 'Batidora',
            'Robot cocina', 'Vajilla completa', 'Cubiertos acero', 'Cacerolas', 'Sartenes'
        ];

        return $items[array_rand($items)];
    }

    private function generateRandomDescription(): string
    {
        $descriptions = [
            'Artículo en buen estado, poco uso. Se entrega por mudanza.',
            'Funciona perfectamente, tiene algunos signos de uso normal.',
            'Como nuevo, prácticamente sin usar. Cambio de decoración.',
            'Buen estado general, ideal para estudiantes o principiantes.',
            'Usado pero funcional, perfecto para segunda vivienda.',
            'Excelente calidad, se regala por falta de espacio.',
            'Estado impecable, comprado hace poco pero no lo necesito.',
            'Tiene algunos años pero funciona muy bien.',
            'Perfecto para manualidades o proyectos DIY.',
            'Se entrega con todos los accesorios originales.'
        ];

        return $descriptions[array_rand($descriptions)];
    }

    private function getRandomCondition(): string
    {
        $conditions = ['nuevo', 'como_nuevo', 'buen_estado', 'usado', 'necesita_reparacion'];
        $weights = [10, 20, 40, 25, 5]; // Más probabilidad para buen_estado

        return $this->weightedRandom($conditions, $weights);
    }

    private function getRandomStatus(): string
    {
        $statuses = ['available', 'reserved', 'given'];
        $weights = [70, 20, 10]; // Mayoría disponibles

        return $this->weightedRandom($statuses, $weights);
    }

    private function getRandomLocation(): string
    {
        $locations = [
            'Madrid Centro', 'Madrid Norte', 'Madrid Sur', 'Barcelona Centro',
            'Barcelona, Eixample', 'Barcelona, Gràcia', 'Valencia Centro',
            'Sevilla Norte', 'Bilbao Centro', 'Zaragoza', 'Málaga',
            'Palencia Centro', 'Valladolid', 'Burgos', 'León',
            'Salamanca', 'Ávila', 'Segovia', 'Soria'
        ];

        return $locations[array_rand($locations)];
    }

    private function weightedRandom(array $values, array $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = mt_rand(1, $totalWeight);

        for ($i = 0; $i < count($values); $i++) {
            $random -= $weights[$i];
            if ($random <= 0) {
                return $values[$i];
            }
        }

        return $values[0];
    }
}
