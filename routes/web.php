<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ItemInterestController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Models\Item;
use App\Models\Request as ItemRequest; // Usamos un alias para evitar conflicto con la clase Request de HTTP
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/broadcast-test', function() {
    // Este evento se transmitirá a todos los usuarios
    event(new \App\Events\TestEvent('Prueba de broadcasting'));
    return "Evento de prueba enviado. Verifica la consola del navegador.";
});

// Ruta principal
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'recentItems' => Item::with('user')
            ->where('status', 'available')
            ->latest()
            ->take(5)
            ->get(),
        'recentRequests' => ItemRequest::with('user')
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get()
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas públicas
Route::resource('items', ItemController::class)->only(['index', 'show']);
Route::resource('categories', CategoryController::class)->only(['index', 'show']);

// Rutas protegidas - requieren autenticación
Route::middleware('auth')->group(function () {
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para items que requieren autenticación
    Route::resource('items', ItemController::class)->except(['index', 'show']);
    Route::patch('/items/{item}/status', [ItemController::class, 'updateStatus'])->name('items.updateStatus');

    // Rutas de ofertas
    Route::resource('offers', OfferController::class);

    // Rutas de solicitudes
    Route::resource('requests', RequestController::class);
    Route::get('/my-requests', [RequestController::class, 'myRequests'])->name('requests.my');

    // Rutas de intereses
    Route::get('/interests', [ItemInterestController::class, 'index'])->name('interests.index');
    Route::post('/interests', [ItemInterestController::class, 'store'])->name('interests.store');
    Route::patch('/interests/{interest}', [ItemInterestController::class, 'update'])->name('interests.update');
    Route::get('/received-interests', [ItemInterestController::class, 'receivedInterests'])->name('interests.received');

    // Rutas de notificaciones
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/api/unread-notifications', [NotificationController::class, 'getUnreadNotifications'])->name('api.unreadNotifications');

    // Rutas para mensajes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{contact}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/{contact}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    Route::get('/items/{item}/message', [MessageController::class, 'createFromItem'])->name('messages.fromItem');
    Route::get('/interests/{interest}/message', [MessageController::class, 'createFromInterest'])->name('messages.fromInterest');

    // Nueva ruta para el conteo de mensajes no leídos
    Route::get('/api/unread-messages-count', [MessageController::class, 'getUnreadCount'])->name('api.unreadMessagesCount');
});

require __DIR__.'/auth.php';
