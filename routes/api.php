<?php

use App\Http\Controllers\Api\FriendshipController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PokeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotificationController;
use Database\Seeders\TestsSeeder;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('/user', [UserController::class, 'user']);
        Route::get('/invites', [UserController::class, 'invites']);
        Route::get('/suggests', [UserController::class, 'suggests']);
        Route::get('/friends/{user}', [UserController::class, 'friends']);

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
        
        Route::get('/pokes', [PokeController::class, 'index']);
        Route::post('/pokes', [PokeController::class, 'store']);
        Route::post('/pokes/{poke}', [PokeController::class, 'update']);

        Route::post('/invite', [FriendshipController::class, 'invite']);
        Route::post('/accept', [FriendshipController::class, 'accept']);
        Route::post('/reject', [FriendshipController::class, 'reject']);
        Route::post('/destroy', [FriendshipController::class, 'destroy']);

        Route::get('/messages/{receiverId}', [MessageController::class, 'index'])->whereNumber('receiverId');
        Route::post('/messages', [MessageController::class, 'store']);
        Route::get('/messenger', [MessageController::class, 'messenger']);

        Broadcast::routes();
    });

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);


Route::get('/tests', function () {
    $seeder = new TestsSeeder();
    $seeder->run();

    return response()->json('Success');
});