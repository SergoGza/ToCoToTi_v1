<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Mostrar todas las notificaciones del usuario.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Marcar una notificación como leída.
     */
    public function markAsRead(Notification $notification)
    {
        // Verificar que la notificación pertenece al usuario actual
        if ($notification->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $notification->read = true;
        $notification->save();

        return redirect()->back();
    }

    /**
     * Marcar todas las notificaciones como leídas.
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->update(['read' => true]);

        return redirect()->back();
    }

    /**
     * Obtener notificaciones no leídas para la barra de navegación.
     */
    public function getUnreadNotifications()
    {
        $notifications = Auth::user()->notifications()
            ->where('read', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $count = Auth::user()->unreadNotificationsCount();

        return response()->json([
            'notifications' => $notifications,
            'count' => $count
        ]);
    }
}
