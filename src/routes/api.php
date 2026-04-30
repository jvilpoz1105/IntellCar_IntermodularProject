<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppUserController;
use App\Http\Controllers\Api\CarAdvertController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\MakeController;
use App\Http\Controllers\Api\PaddockController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| 
| Distrito 0: Autenticación
| Rutas públicas y protegidas de autenticación
*/

// Rutas Públicas de Autenticación
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Rutas Protegidas (Requieren Token)
Route::middleware('auth:sanctum')->group(function () {
    
    // Autenticación
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // CRUD de Usuarios
    Route::get('/users', [AppUserController::class, 'index'])->middleware('role:admin');
    Route::get('/users/{id}', [AppUserController::class, 'show']);
    Route::put('/users/{id}', [AppUserController::class, 'update']);
    Route::patch('/users/{id}', [AppUserController::class, 'update']);
    Route::delete('/users/{id}', [AppUserController::class, 'destroy'])->middleware('role:admin');
});

// Rutas públicas de la plataforma
Route::get('/makes', [MakeController::class, 'index']);
Route::get('/makes/{id}', [MakeController::class, 'show']);
Route::get('/paddocks', [PaddockController::class, 'index']);
Route::get('/paddocks/{id}', [PaddockController::class, 'show']);
Route::get('/adverts', [CarAdvertController::class, 'index']);
Route::get('/adverts/{id}', [CarAdvertController::class, 'show']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);

// Rutas protegidas de la plataforma
Route::middleware('auth:sanctum')->group(function () {
    // Anuncios de vehículos
    Route::post('/adverts', [CarAdvertController::class, 'store']);
    Route::put('/adverts/{id}', [CarAdvertController::class, 'update']);
    Route::delete('/adverts/{id}', [CarAdvertController::class, 'destroy']);

    // Posts (El Universo)
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts/{id}/like', [PostController::class, 'like']);
    Route::delete('/posts/{id}/like', [PostController::class, 'unlike']);

    // Eventos (Kdds)
    Route::post('/events', [EventController::class, 'store']);
    Route::post('/events/{id}/join', [EventController::class, 'join']);
    Route::delete('/events/{id}/leave', [EventController::class, 'leave']);
});

