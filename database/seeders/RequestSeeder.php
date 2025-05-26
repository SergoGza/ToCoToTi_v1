<?php

namespace Database\Seeders;

use App\Models\Request as ItemRequest;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class RequestSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        // Solicitudes específicas con datos realistas
        $specificRequests = [
            [
                'title' => 'Busco silla de escritorio ergonómica',
                'description' => 'Necesito una silla de oficina cómoda para trabajar desde casa. No importa el color, lo importante es que sea ergonómica y esté en buen estado.',
                'status' => 'active',
                'location' => 'Madrid',
                'expires_at' => now()->addMonth(),
                'category_id' => $categories->where('name', 'Muebles')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Necesito libros de programación',
                'description' => 'Estoy aprendiendo programación y busco libros sobre JavaScript, Python o desarrollo web en general. Cualquier nivel es bienvenido.',
                'status' => 'active',
                'location' => 'Barcelona',
                'expires_at' => now()->addWeeks(3),
                'category_id' => $categories->where('name', 'Libros')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Ropa de niño talla 4-6 años',
                'description' => 'Busco ropa para mi hijo que está creciendo muy rápido. Pantalones, camisetas, jerseys... cualquier cosa en talla 4-6 años será muy útil.',
                'status' => 'active',
                'location' => 'Valencia',
                'expires_at' => now()->addWeeks(2),
                'category_id' => $categories->where('name', 'Ropa')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Bicicleta para adulto (cualquier estado)',
                'description' => 'Busco una bicicleta para desplazarme al trabajo. No importa si necesita algunas reparaciones, yo mismo puedo arreglarla.',
                'status' => 'active',
                'location' => 'Sevilla',
                'expires_at' => now()->addMonth(),
                'category_id' => $categories->where('name', 'Deportes')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Tablet o iPad para estudiar',
                'description' => 'Soy estudiante y necesito una tablet para tomar apuntes y leer PDFs. No necesita ser de última generación, solo que funcione bien.',
                'status' => 'active',
                'location' => 'Bilbao',
                'expires_at' => now()->addWeeks(4),
                'category_id' => $categories->where('name', 'Electrónica')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Juguetes educativos para niña de 5 años',
                'description' => 'Busco juguetes educativos, puzzles, juegos de construcción o similares para mi hija. Queremos fomentar su creatividad y aprendizaje.',
                'status' => 'fulfilled',
                'location' => 'Zaragoza',
                'expires_at' => now()->addWeek(),
                'category_id' => $categories->where('name', 'Juguetes')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Herramientas básicas de bricolaje',
                'description' => 'Me acabo de independizar y necesito herramientas básicas: martillo, destornilladores, llaves... Empezando desde cero con el bricolaje casero.',
                'status' => 'active',
                'location' => 'Málaga',
                'expires_at' => now()->addWeeks(2),
                'category_id' => $categories->where('name', 'Herramientas')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Microondas que funcione',
                'description' => 'Se me ha estropeado el microondas y busco uno de repuesto. No importa la marca ni el año, solo que caliente bien la comida.',
                'status' => 'active',
                'location' => 'Palencia',
                'expires_at' => now()->addWeek(),
                'category_id' => $categories->where('name', 'Electrodomésticos')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Mesa pequeña para balcón',
                'description' => 'Busco una mesa pequeña para poner en el balcón y poder desayunar al aire libre. Preferiblemente que resista la intemperie.',
                'status' => 'active',
                'location' => 'Madrid',
                'expires_at' => now()->addWeeks(3),
                'category_id' => $categories->where('name', 'Muebles')->first()?->id,
                'user_id' => $users->random()->id,
            ],
            [
                'title' => 'Productos de segunda mano para hogar',
                'description' => 'Acabamos de mudarnos y necesitamos cualquier cosa para el hogar: utensilios de cocina, decoración, pequeños electrodomésticos...',
                'status' => 'active',
                'location' => 'Barcelona',
                'expires_at' => now()->addMonth(),
                'category_id' => $categories->where('name', 'Otros')->first()?->id,
                'user_id' => $users->random()->id,
            ],
        ];

        // Crear solicitudes específicas
        foreach ($specificRequests as $requestData) {
            ItemRequest::create($requestData);
        }

        // Crear solicitudes adicionales aleatorias
        for ($i = 0; $i < 15; $i++) {
            ItemRequest::create([
                'title' => $this->generateRandomRequestTitle(),
                'description' => $this->generateRandomRequestDescription(),
                'status' => $this->getRandomStatus(),
                'location' => $this->getRandomLocation(),
                'expires_at' => $this->getRandomExpiration(),
                'category_id' => $categories->random()->id,
                'user_id' => $users->random()->id,
            ]);
        }
    }

    private function generateRandomRequestTitle(): string
    {
        $prefixes = ['Busco', 'Necesito', 'Solicito', 'Me haría falta'];
        $items = [
            'muebles para estudiante', 'ropa de temporada', 'libros de cocina',
            'herramientas de jardín', 'juguetes para niños', 'electrodomésticos pequeños',
            'equipo deportivo', 'material de oficina', 'decoración hogar',
            'dispositivos electrónicos', 'instrumentos musicales', 'material escolar',
            'accesorios para mascotas', 'plantas de interior', 'utensilios cocina'
        ];

        return $prefixes[array_rand($prefixes)] . ' ' . $items[array_rand($items)];
    }

    private function generateRandomRequestDescription(): string
    {
        $descriptions = [
            'Estoy empezando y necesito este tipo de artículos. Cualquier estado es bienvenido.',
            'Por favor, si tienes algo similar que ya no uses, me vendría muy bien.',
            'Busco para uso personal, no importa si tiene algún defecto menor.',
            'Agradecería mucho cualquier ayuda, es para un proyecto personal.',
            'Necesito urgentemente, dispuesto a recoger en cualquier momento.',
            'Si tienes algo parecido guardado, te lo agradecería enormemente.',
            'Empezando nueva etapa y necesito equiparme básicamente.',
            'Cualquier aportación será muy valorada y bien cuidada.',
            'Es para uso familiar, cuidaremos muy bien lo que nos donen.',
            'Si ya no lo necesitas, para mí sería de gran utilidad.'
        ];

        return $descriptions[array_rand($descriptions)];
    }

    private function getRandomStatus(): string
    {
        $statuses = ['active', 'fulfilled', 'cancelled'];
        $weights = [80, 15, 5]; // Mayoría activas

        return $this->weightedRandom($statuses, $weights);
    }

    private function getRandomLocation(): string
    {
        $locations = [
            'Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao',
            'Zaragoza', 'Málaga', 'Palencia', 'Valladolid', 'León',
            'Burgos', 'Salamanca', 'Ávila', 'Segovia', 'Soria'
        ];

        return $locations[array_rand($locations)];
    }

    private function getRandomExpiration(): ?\DateTime
    {
        // 30% sin fecha de expiración, 70% con fecha aleatoria en el futuro
        if (mt_rand(1, 100) <= 30) {
            return null;
        }

        $daysFromNow = mt_rand(7, 60); // Entre 1 semana y 2 meses
        return now()->addDays($daysFromNow);
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
