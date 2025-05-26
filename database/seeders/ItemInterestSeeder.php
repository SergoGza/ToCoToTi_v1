<?php

namespace Database\Seeders;

use App\Models\ItemInterest;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class ItemInterestSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $availableItems = Item::where('status', 'available')->get();
        $reservedItems = Item::where('status', 'reserved')->get();

        // Crear intereses para items disponibles
        foreach ($availableItems->take(15) as $item) {
            // Cada item disponible puede tener entre 1 y 4 intereses
            $interestCount = mt_rand(1, 4);
            $interestedUsers = $users->where('id', '!=', $item->user_id)->random($interestCount);

            foreach ($interestedUsers as $user) {
                ItemInterest::create([
                    'item_id' => $item->id,
                    'user_id' => $user->id,
                    'message' => $this->generateInterestMessage(),
                    'status' => 'pending',
                ]);
            }
        }

        // Crear intereses para items reservados (uno aceptado, otros rechazados)
        foreach ($reservedItems as $item) {
            $interestCount = mt_rand(2, 5);
            $interestedUsers = $users->where('id', '!=', $item->user_id)->random($interestCount);

            foreach ($interestedUsers as $index => $user) {
                ItemInterest::create([
                    'item_id' => $item->id,
                    'user_id' => $user->id,
                    'message' => $this->generateInterestMessage(),
                    'status' => $index === 0 ? 'accepted' : 'rejected', // Primer usuario aceptado
                ]);
            }
        }

        // Algunos intereses adicionales con mensajes específicos
        $specificInterests = [
            [
                'message' => 'Me interesa mucho este artículo. Podría recogerlo hoy mismo si te parece bien.',
                'status' => 'pending'
            ],
            [
                'message' => 'Hola, soy estudiante y esto me vendría perfecto para mi proyecto. ¿Sigue disponible?',
                'status' => 'pending'
            ],
            [
                'message' => 'Buenos días, me encantaría poder darle una segunda vida a este objeto. Gracias por compartir.',
                'status' => 'pending'
            ],
            [
                'message' => 'Es exactamente lo que estaba buscando. Vivo cerca, puedo pasar a recogerlo cuando te vaya bien.',
                'status' => 'accepted'
            ],
            [
                'message' => 'Mi hijo está empezando con esta afición y esto le vendría genial. ¿Podríamos quedar?',
                'status' => 'pending'
            ],
        ];

        $remainingItems = $availableItems->skip(15)->take(5);
        foreach ($remainingItems as $index => $item) {
            if ($index < count($specificInterests)) {
                $randomUser = $users->where('id', '!=', $item->user_id)->random();

                ItemInterest::create([
                    'item_id' => $item->id,
                    'user_id' => $randomUser->id,
                    'message' => $specificInterests[$index]['message'],
                    'status' => $specificInterests[$index]['status'],
                ]);
            }
        }
    }

    private function generateInterestMessage(): string
    {
        $messages = [
            'Me interesa este artículo. ¿Sigue disponible?',
            'Hola, ¿podríamos quedar para verlo? Me vendría muy bien.',
            'Perfecto para lo que necesito. ¿Cuándo podría recogerlo?',
            'Muchas gracias por compartir. Me haría mucha ilusión poder recogerlo.',
            'Es justo lo que estaba buscando. ¿Dónde podríamos quedar?',
            'Me vendría genial para mi proyecto. ¿Podemos hablar por privado?',
            'Qué casualidad encontrar esto. ¿Sigue disponible para recoger?',
            'Estoy muy interesado. Puedo recogerlo cuando mejor te vaya.',
            'Es perfecto para lo que necesito. ¿Podemos contactar por mensaje?',
            '¡Increíble! Es exactamente lo que buscaba. ¿Cómo quedamos?',
            'Me encanta la idea de reutilizar. ¿Cuándo puedo pasarlo a buscar?',
            'Vivo cerca de tu zona. ¿Podríamos quedar esta semana?',
            'Es para un proyecto personal muy importante para mí.',
            'Mi familia podría aprovecharlo muy bien. Gracias por compartir.',
            'Soy muy cuidadoso con las cosas. Le daría buen uso.',
            '', // Algunos intereses sin mensaje
            '',
            '',
        ];

        return $messages[array_rand($messages)];
    }
}
