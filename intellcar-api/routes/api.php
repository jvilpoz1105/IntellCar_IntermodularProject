<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarAdvertController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\MakeController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PaddockController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí se definen todas las rutas de la API REST de IntellCar.
| Estas rutas son protegidas con Laravel Sanctum para autenticación por tokens.
|
*/

// ============================================================================
// RUTAS PÚBLICAS (sin autenticación)
// ============================================================================

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
});

// Rutas públicas de consulta
Route::get('/adverts', [CarAdvertController::class, 'index'])->name('api.adverts.index');
Route::get('/adverts/{id}', [CarAdvertController::class, 'show'])->name('api.adverts.show');

Route::get('/posts', [PostController::class, 'index'])->name('api.posts.index');
Route::get('/posts/{id}', [PostController::class, 'show'])->name('api.posts.show');

Route::get('/makes', [MakeController::class, 'index'])->name('api.makes.index');
Route::get('/makes/{id}', [MakeController::class, 'show'])->name('api.makes.show');

Route::get('/events', [EventController::class, 'index'])->name('api.events.index');
Route::get('/events/{id}', [EventController::class, 'show'])->name('api.events.show');

Route::get('/paddocks', [PaddockController::class, 'index'])->name('api.paddocks.index');
Route::get('/paddocks/{id}', [PaddockController::class, 'show'])->name('api.paddocks.show');

// ============================================================================
// RUTAS PROTEGIDAS (requieren autenticación con token)
// ============================================================================

Route::middleware('auth:sanctum')->group(function () {
    
    // === AUTENTICACIÓN ===
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::get('/me', [AuthController::class, 'me'])->name('api.auth.me');
    });

    // === ANUNCIOS ===
    Route::prefix('adverts')->group(function () {
        Route::post('/', [CarAdvertController::class, 'store'])->name('api.adverts.store');
        Route::put('/{id}', [CarAdvertController::class, 'update'])->name('api.adverts.update');
        Route::delete('/{id}', [CarAdvertController::class, 'destroy'])->name('api.adverts.destroy');
    });

    // === POSTS ===
    Route::prefix('posts')->group(function () {
        Route::post('/', [PostController::class, 'store'])->name('api.posts.store');
        Route::put('/{id}', [PostController::class, 'update'])->name('api.posts.update');
        Route::delete('/{id}', [PostController::class, 'destroy'])->name('api.posts.destroy');
        
        // Likes
        Route::post('/{id}/like', [PostController::class, 'like'])->name('api.posts.like');
        Route::delete('/{id}/like', [PostController::class, 'unlike'])->name('api.posts.unlike');
    });

    // === EVENTOS ===
    Route::prefix('events')->group(function () {
        Route::post('/', [EventController::class, 'store'])->name('api.events.store');
        Route::post('/{id}/join', [EventController::class, 'join'])->name('api.events.join');
        Route::delete('/{id}/leave', [EventController::class, 'leave'])->name('api.events.leave');
    });

});

// ============================================================================
// RUTAS SOLO PARA ADMINISTRADORES
// ============================================================================

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Aquí se pueden añadir rutas específicas para administradores
    // Por ejemplo: gestión de usuarios, aprobación de anuncios, etc.
});
