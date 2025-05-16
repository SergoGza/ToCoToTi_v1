<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Request as ItemRequest;
use App\Models\User;
use App\Notifications\MatchingItemsFound;
use App\Services\MatchingService;
use Illuminate\Console\Command;

class FindItemRequestMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:find-matches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Busca coincidencias entre ítems y solicitudes y notifica a los usuarios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $matchingService = new MatchingService();

        // Procesar solicitudes activas para buscar ítems que coincidan
        $this->processActiveRequests($matchingService);

        // También podríamos procesar nuevos ítems para buscar solicitudes coincidentes
        // $this->processNewItems($matchingService);

        $this->info('Proceso de búsqueda de coincidencias completado');

        return Command::SUCCESS;
    }

    /**
     * Procesar solicitudes activas para buscar ítems coincidentes.
     */
    protected function processActiveRequests(MatchingService $matchingService)
    {
        // Obtener solicitudes activas que no hayan sido notificadas recientemente
        $requests = ItemRequest::where('status', 'active')
            ->whereDoesntHave('notifications', function($query) {
                // No enviar notificaciones para la misma solicitud más de una vez al día
                $query->where('created_at', '>=', now()->subDay());
            })
            ->with('user')
            ->get();

        $this->info("Procesando {$requests->count()} solicitudes activas");

        foreach ($requests as $request) {
            // Buscar ítems que coincidan con esta solicitud
            $matchingItems = $matchingService->findMatchingItems($request);

            if ($matchingItems->count() > 0) {
                $this->info("Encontradas {$matchingItems->count()} coincidencias para la solicitud #{$request->id}");

                // Notificar al usuario si hay coincidencias
                $request->user->notify(new MatchingItemsFound($matchingItems, $request));

                // Guardar relación de que esta solicitud ha sido notificada
                $request->notifications()->create([
                    'created_at' => now()
                ]);
            }
        }
    }

    /**
     * Procesar ítems recientes para buscar solicitudes coincidentes.
     */
    protected function processNewItems(MatchingService $matchingService)
    {
        // Obtener ítems recientemente creados o actualizados (últimas 24 horas)
        $items = Item::where('status', 'available')
            ->where(function($query) {
                $query->where('created_at', '>=', now()->subDay())
                    ->orWhere('updated_at', '>=', now()->subDay());
            })
            ->get();

        $this->info("Procesando {$items->count()} ítems recientes");

        foreach ($items as $item) {
            // Buscar solicitudes que coincidan con este ítem
            $matchingRequests = $matchingService->findMatchingRequests($item);

            if ($matchingRequests->count() > 0) {
                $this->info("Encontradas {$matchingRequests->count()} solicitudes coincidentes para el ítem #{$item->id}");

                // Implementar notificación para solicitudes coincidentes si lo necesitas
                // Similar a MatchingItemsFound pero para solicitudes
            }
        }
    }
}
