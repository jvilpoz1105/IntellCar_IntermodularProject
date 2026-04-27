<?php

use App\Http\Controllers\Api\AuthController;
<<<<<<< HEAD
use App\Http\Controllers\Api\CarAdvertController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\MakeController;
use App\Http\Controllers\Api\PaddockController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::get('/adverts', [CarAdvertController::class, 'index']);
Route::get('/adverts/{id}', [CarAdvertController::class, 'show']);
Route::get('/makes', [MakeController::class, 'index']);
Route::get('/makes/{id}', [MakeController::class, 'show']);
Route::get('/paddocks', [PaddockController::class, 'index']);
Route::get('/paddocks/{id}', [PaddockController::class, 'show']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/adverts', [CarAdvertController::class, 'store']);
    Route::put('/adverts/{id}', [CarAdvertController::class, 'update']);
    Route::delete('/adverts/{id}', [CarAdvertController::class, 'destroy']);

    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/posts/{id}/like', [PostController::class, 'like']);
    Route::delete('/posts/{id}/like', [PostController::class, 'unlike']);

    Route::post('/events', [EventController::class, 'store']);
    Route::post('/events/{id}/join', [EventController::class, 'join']);
    Route::delete('/events/{id}/leave', [EventController::class, 'leave']);
=======
use App\Http\Controllers\Api\AppUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas Públicas de Autenticación
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Rutas Protegidas (Requieren Token)
Route::middleware('auth:sanctum')->group(function () {
    
    // Perfil del usuario actual
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // CRUD de Usuarios
    // Solo admins pueden ver la lista completa
    Route::get('/users', [AppUserController::class, 'index'])->middleware('role:admin');
    
    // Ver un usuario específico
    Route::get('/users/{id}', [AppUserController::class, 'show']);
    
    // Editar un usuario (Lógica de permiso dentro del controlador: dueño o admin)
    Route::put('/users/{id}', [AppUserController::class, 'update']);
    Route::patch('/users/{id}', [AppUserController::class, 'update']);
    
    // Borrar un usuario (Solo Admin)
    Route::delete('/users/{id}', [AppUserController::class, 'destroy'])->middleware('role:admin');

>>>>>>> origin/main
});
