<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ItemInterestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\WelcomeTourController;
use App\Models\Item;
use App\Models\ItemInterest;
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
    $user = Auth::user();

    // Estadísticas del usuario
    $stats = [
        'itemsPublished' => Item::where('user_id', $user->id)->count(),
        'itemsGiven' => Item::where('user_id', $user->id)->where('status', 'given')->count(),
        'requestsActive' => ItemRequest::where('user_id', $user->id)->where('status', 'active')->count(),
        'interestsReceived' => ItemInterest::whereHas('item', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count(),
    ];

    // Items del usuario
    $userItems = Item::where('user_id', $user->id)
        ->with('category')
        ->latest()
        ->take(10)
        ->get();

    // Items recientes de la comunidad (no del usuario actual)
    $recentItems = Item::with('user', 'category')
        ->where('status', 'available')
        ->where('user_id', '!=', $user->id)
        ->latest()
        ->take(6)
        ->get();

    // Solicitudes recientes de la comunidad
    $recentRequests = ItemRequest::with('user', 'category')
        ->where('status', 'active')
        ->where('user_id', '!=', $user->id)
        ->latest()
        ->take(6)
        ->get();

    // Items recomendados
    $userCategories = ItemInterest::where('item_interests.user_id', $user->id)
        ->join('items', 'item_interests.item_id', '=', 'items.id')
        ->pluck('items.category_id')
        ->unique();

    $matchingItems = Item::with('user', 'category')
        ->where('status', 'available')
        ->where('user_id', '!=', $user->id)
        ->when($userCategories->isNotEmpty(), function($query) use ($userCategories) {
            $query->whereIn('category_id', $userCategories);
        })
        ->inRandomOrder()
        ->take(4)
        ->get();

    return Inertia::render('Dashboard', [
        'stats' => $stats,
        'userItems' => $userItems,
        'recentItems' => $recentItems,
        'recentRequests' => $recentRequests,
        'matchingItems' => $matchingItems,
        'showWelcomeTour' => !$user->hasCompletedWelcomeTour(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas públicas para items
Route::get('items', [ItemController::class, 'index'])->name('items.index');

// Rutas protegidas - requieren autenticación
Route::middleware('auth')->group(function () {
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para items que requieren autenticación
    Route::get('items/my', [ItemController::class, 'myItems'])->name('items.my'); // NUEVA RUTA
    Route::get('items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('items', [ItemController::class, 'store'])->name('items.store');
    Route::get('items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::patch('items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::patch('/items/{item}/status', [ItemController::class, 'updateStatus'])->name('items.updateStatus');
    Route::get('items/{item}', [ItemController::class, 'show'])->name('items.show');

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

    // Rutas para el tour de bienvenida
    Route::post('/welcome-tour/complete', [WelcomeTourController::class, 'complete'])->name('welcome-tour.complete');
    Route::get('/api/welcome-tour/status', [WelcomeTourController::class, 'status'])->name('api.welcome-tour.status');
});

require __DIR__.'/auth.php';
