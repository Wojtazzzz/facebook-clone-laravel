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
            ->prefix('/friendship')
            ->group(function () {
                Route::get('/friends/{user}', 'friends');
                Route::get('/suggests', 'suggests');
                Route::get('/invites', 'invites');
                
                Route::post('/invite', 'invite');
                Route::post('/accept', 'accept');
                Route::post('/reject', 'reject');
                Route::post('/destroy', 'destroy');
            });

        Route::controller(PokeController::class)
            ->group(function () {
                Route::get('/pokes', 'index');
                Route::post('/pokes', 'store');
                Route::post('/pokes/{poke}', 'update');
            });

        Route::controller(NotificationController::class)
            ->group(function () {
                Route::get('/notifications', 'index');
                Route::post('/notifications/mark-as-read', 'markAsRead');
            });

        Route::controller(MessageController::class)
            ->prefix('/messages')
            ->group(function () {
                Route::get('/{receiverId}', 'index')->whereNumber('receiverId');
                Route::post('/', 'store');
                Route::get('/messenger', 'messenger');
            });

        Broadcast::routes();
    });

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);


Route::get('/tests', function () {
    $seeder = new TestsSeeder();
    $seeder->run();

    return response()->json('Success');
});