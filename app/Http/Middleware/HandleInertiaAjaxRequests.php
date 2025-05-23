<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaAjaxRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si es una petición Inertia parcial (AJAX), asegurarse de que tenga el token CSRF
        if ($request->header('X-Inertia')) {
            // Verificar que la sesión esté activa
            if (!$request->hasSession() || !$request->session()->token()) {
                return response()->json(['message' => 'CSRF token mismatch.'], 419);
            }
        }

        return $next($request);
    }
}
