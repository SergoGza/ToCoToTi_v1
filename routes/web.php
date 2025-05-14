<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ItemInterestController;
use App\Http\Controllers\CategoryController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Ruta principal - podemos personalizarla para mostrar productos recientes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas protegidas - requieren autenticación
Route::middleware('auth')->group(function () {
    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para nuestros recursos que requieren autenticación
    Route::resource('items', ItemController::class)->except(['index', 'show']);
    Route::resource('offers', OfferController::class);
    Route::resource('requests', RequestController::class);
    Route::resource('interests', ItemInterestController::class);
});

// Rutas públicas
Route::resource('items', ItemController::class)->only(['index', 'show']);
Route::resource('categories', CategoryController::class)->only(['index', 'show']);

// Rutas de intereses
Route::get('/interests', [ItemInterestController::class, 'index'])->name('interests.index');
Route::post('/interests', [ItemInterestController::class, 'store'])->name('interests.store');
Route::patch('/interests/{interest}', [ItemInterestController::class, 'update'])->name('interests.update');


require __DIR__.'/auth.php';
