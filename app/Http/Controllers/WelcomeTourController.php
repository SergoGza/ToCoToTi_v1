<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeTourController extends Controller
{
    /**
     * Marcar el tour de bienvenida como completado para el usuario actual
     */
    public function complete(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->markWelcomeTourCompleted();

            return response()->json([
                'success' => true,
                'message' => 'Tour de bienvenida completado'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Usuario no autenticado'
        ], 401);
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
