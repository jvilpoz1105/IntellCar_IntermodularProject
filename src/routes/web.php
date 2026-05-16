<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

        // Eventos
        Route::get('/events',                           [AdminController::class, 'eventsIndex'])->name('events.index');
        Route::get('/events/{event}',                   [AdminController::class, 'eventsShow'])->name('events.show');
        Route::patch('/events/{event}/toggle-visible',  [AdminController::class, 'eventsToggleVisible'])->name('events.toggle-visible');
        Route::delete('/events/{event}',                [AdminController::class, 'eventsDestroy'])->name('events.destroy');

        // Posts
        Route::get('/posts',                            [AdminController::class, 'postsIndex'])->name('posts.index');
        Route::get('/posts/{post}',                     [AdminController::class, 'postsShow'])->name('posts.show');
        Route::patch('/posts/{post}/toggle-visible',    [AdminController::class, 'postsToggleVisible'])->name('posts.toggle-visible');
        Route::delete('/posts/{post}',                  [AdminController::class, 'postsDestroy'])->name('posts.destroy');
    });
});
