<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'IntellCar API v1']);
});

require __DIR__.'/auth.php';

// ────────────────────────────────────────────────
// Admin panel (Blade / session-based)
// ────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    // Públicas (login)
    Route::get('/login',  [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    // Protegidas con AdminWebMiddleware
    Route::middleware('admin.web')->group(function () {
        Route::get('/',        [AdminController::class, 'dashboard'])->name('dashboard');

        // Usuarios
        Route::get('/users',                          [AdminController::class, 'usersIndex'])->name('users.index');
        Route::get('/users/{user}',                   [AdminController::class, 'usersShow'])->name('users.show');
        Route::patch('/users/{user}/toggle-active',   [AdminController::class, 'usersToggleActive'])->name('users.toggle-active');
        Route::delete('/users/{user}',                [AdminController::class, 'usersDestroy'])->name('users.destroy');

        // Anuncios
        Route::get('/adverts',                          [AdminController::class, 'advertsIndex'])->name('adverts.index');
        Route::get('/adverts/{advert}',                 [AdminController::class, 'advertsShow'])->name('adverts.show');
        Route::patch('/adverts/{advert}/toggle-visible',[AdminController::class, 'advertsToggleVisible'])->name('adverts.toggle-visible');
        Route::delete('/adverts/{advert}',              [AdminController::class, 'advertsDestroy'])->name('adverts.destroy');
    });
});
