<?php

use App\Http\Controllers\Api\AuthController;
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

});
