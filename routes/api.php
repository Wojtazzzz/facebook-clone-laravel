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

        Route::controller(UserController::class)
            ->group(function () {
                Route::get('/user', 'user');
            });

        Route::controller(FriendshipController::class)
            ->group(function () {
                Route::get('/friends/{user}', 'friends');
                Route::get('/suggests', 'suggests');
                Route::get('/invites', 'invites');
            });

        Route::controller(PokeController::class)
            ->group(function () {
                Route::get('/pokes', 'index');
                Route::post('/pokes', 'store');
                Route::post('/pokes/{poke}', 'update');
            });

        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);

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