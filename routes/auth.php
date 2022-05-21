<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthenticatedSessionController::class)
    ->group(function () {
        Route::post('/login', 'store')->middleware('guest');
        Route::post('/logout', 'destroy')->middleware('auth');
});

Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');