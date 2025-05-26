<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WelcomeTourController extends Controller
{
    /**
     * Marcar el tour de bienvenida como completado para el usuario actual
     */
    public function complete(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                Log::warning('WelcomeTour: Usuario no autenticado');
                return back()->with('error', 'Usuario no autenticado');
            }

            Log::info('WelcomeTour: Completando tour para usuario', ['user_id' => $user->id]);

            // Marcar como completado
            $user->markWelcomeTourCompleted();

            Log::info('WelcomeTour: Tour completado exitosamente', [
                'user_id' => $user->id,
                'welcome_tour_completed' => $user->fresh()->welcome_tour_completed
            ]);

            // Para peticiones Inertia, devolver redirección con mensaje de éxito
            return back()->with('success', 'Tour de bienvenida completado');

        } catch (\Exception $e) {
            Log::error('WelcomeTour: Error al completar tour', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al completar el tour');
        }
    }

    /**
     * Verificar si el usuario actual ha completado el tour
     */
    public function status(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            return response()->json([
                'completed' => $user->hasCompletedWelcomeTour()
            ]);
        }

        return response()->json([
            'completed' => false
        ]);
    }
}
