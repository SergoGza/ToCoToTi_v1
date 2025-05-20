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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

// Rutas públicas para items - Modificado para evitar conflictos
Route::get('items', [ItemController::class, 'index'])->name('items.index');


// Rutas públicas para categorías
Route::resource('categories', CategoryController::class)->only(['index', 'show']);

// Rutas protegidas - requieren autenticación
Route::middleware('auth')->group(function () {
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para items que requieren autenticación - Modificado para evitar conflictos
    Route::get('items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('items', [ItemController::class, 'store'])->name('items.store');
    Route::get('items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::patch('items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::patch('/items/{item}/status', [ItemController::class, 'updateStatus'])->name('items.updateStatus');
    Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');

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

    // Rutas de mensajería
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'sendMessage'])->name('messages.send');
    Route::get('/messages/user/{otherUser}', [MessageController::class, 'startConversation'])->name('messages.start');
    Route::get('/messages/from-item/{item}', [MessageController::class, 'fromItem'])->name('messages.fromItem');
    Route::get('/messages/from-interest/{interest}', [MessageController::class, 'fromInterest'])->name('messages.fromInterest');

    // API para el conteo de mensajes no leídos
    Route::get('/api/unread-messages', function () {
        $user = Auth::user();
        $count = $user ? $user->unreadMessagesCount() : 0;
        return response()->json(['count' => $count]);
    })->name('api.unreadMessages');
});

require __DIR__.'/auth.php';
