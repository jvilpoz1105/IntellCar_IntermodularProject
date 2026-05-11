<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'IntellCar API v1']);
});

require __DIR__.'/auth.php';
