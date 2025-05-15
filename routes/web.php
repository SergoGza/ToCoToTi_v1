<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ItemInterestController;
use App\Http\Controllers\CategoryController;
use App\Models\Item;
use App\Models\Request as ItemRequest; // Usamos un alias para evitar conflicto con la clase Request de HTTP
use Illuminate\Foundation\Application;
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

    // Rutas de ofertas
    Route::resource('offers', OfferController::class);

    // Rutas de solicitudes
    Route::resource('requests', RequestController::class);
    Route::get('/my-requests', [RequestController::class, 'myRequests'])->name('requests.my');

    // Rutas de intereses
    Route::get('/interests', [ItemInterestController::class, 'index'])->name('interests.index');
    Route::post('/interests', [ItemInterestController::class, 'store'])->name('interests.store');
    Route::patch('/interests/{interest}', [ItemInterestController::class, 'update'])->name('interests.update');
});

require __DIR__.'/auth.php';
