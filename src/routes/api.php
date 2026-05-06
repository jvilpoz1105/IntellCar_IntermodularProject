<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AppUserController;
use App\Http\Controllers\Api\MarketControl;
use App\Http\Controllers\Api\UnivControl;
use App\Http\Controllers\Api\KddControl;
use App\Http\Controllers\Api\ProfileControl;
use Illuminate\Http\Request;
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

// --- RUTAS PÚBLICAS DE SERVICIOS (Lectura) ---

// Market (Anuncios)
Route::get('/market', [MarketControl::class, 'index']);
Route::get('/market/{id}', [MarketControl::class, 'show']);

// Social (Posts)
Route::get('/social', [UnivControl::class, 'index']);
Route::get('/social/{id}', [UnivControl::class, 'show']);

// Eventos (Kdds)
Route::get('/kdds', [KddControl::class, 'index']);
Route::get('/kdds/{id}', [KddControl::class, 'show']);


// --- RUTAS PROTEGIDAS (Requieren Token) ---
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
    
    // --- NUEVO: GESTIÓN DEL PERFIL PROPIO ---
    Route::get('/profile', [ProfileControl::class, 'show']);
    Route::put('/profile', [ProfileControl::class, 'update']);
    Route::post('/profile/picture', [ProfileControl::class, 'updatePicture']);
    Route::patch('/profile/soft-delete', [ProfileControl::class, 'softDelete']);
    
    // --- NUEVO: GESTIÓN DEL GARAJE ---
    Route::post('/profile/garage', [ProfileControl::class, 'addGarageItem']);
    // Usamos POST para updateGarageItem porque enviar archivos vía PUT en PHP multipart/form-data a veces falla, 
    // pero Laravel admite POST con _method=PUT en la request. Aquí lo dejamos en POST para simplificar envío de fotos.
    Route::post('/profile/garage/{id}', [ProfileControl::class, 'updateGarageItem']); 
    Route::delete('/profile/garage/{id}', [ProfileControl::class, 'removeGarageItem']);
    
    // Borrar un usuario (Solo Admin)
    Route::delete('/users/{id}', [ProfileControl::class, 'destroy'])->middleware('role:admin');

    // --- ACTUALIZACIONES DE SERVICIOS (Requieren Token y ser Dueño/Admin) ---
    // NOTA: La lógica de si es "tuyo" ya está dentro de la función update() del controlador
    Route::put('/market/{id}', [MarketControl::class, 'update']);
    Route::patch('/market/{id}', [MarketControl::class, 'update']);
    
    Route::put('/social/{id}', [UnivControl::class, 'update']);
    Route::patch('/social/{id}', [UnivControl::class, 'update']);
    
    Route::put('/kdds/{id}', [KddControl::class, 'update']);
    Route::patch('/kdds/{id}', [KddControl::class, 'update']);

    // --- SOFT DELETES (Solicitud de borrado por el usuario) ---
    Route::patch('/market/{id}/soft-delete', [MarketControl::class, 'softDelete']);
    Route::patch('/social/{id}/soft-delete', [UnivControl::class, 'softDelete']);
    Route::patch('/kdds/{id}/soft-delete', [KddControl::class, 'softDelete']);

    // --- ELIMINACIONES DE SERVICIOS (Solo Admins) ---
    Route::delete('/market/{id}', [MarketControl::class, 'destroy'])->middleware('role:admin');
    Route::delete('/social/{id}', [UnivControl::class, 'destroy'])->middleware('role:admin');
    Route::delete('/kdds/{id}', [KddControl::class, 'destroy'])->middleware('role:admin');

});
