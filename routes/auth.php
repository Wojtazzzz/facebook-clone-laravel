<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')
    ->controller(AuthenticatedSessionController::class)
    ->group(function () {
        Route::post('/login', 'store')
            ->middleware('guest')
            ->name('api.auth.login');

        Route::post('/logout', 'destroy')
            ->middleware('auth')
            ->name('api.auth.logout');
    });

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('api.auth.register');
