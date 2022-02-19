<?php

use App\Http\Controllers\Api\FriendshipController;
use App\Http\Controllers\Api\UserController;
use Database\Seeders\TestsSeeder;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('/user', [UserController::class, 'user']);
        Route::get('/invites', [UserController::class, 'invites']);
        Route::get('/suggests', [UserController::class, 'suggests']);
        Route::get('/friends', [UserController::class, 'friends']);

        Route::post('/invite', [FriendshipController::class, 'invite']);
    });


Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);



Route::get('/tests', function () {
    $seeder = new TestsSeeder();
    $seeder->run();

    return response()->json('Success');
});