<?php

use App\Http\Controllers\Api\FriendshipController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\UserController;
use Database\Seeders\TestsSeeder;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('/user', [UserController::class, 'user']);
        Route::get('/invites', [UserController::class, 'invites']);
        Route::get('/suggests', [UserController::class, 'suggests']);
        Route::get('/friends/{user}', [UserController::class, 'friends']);

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

// https://pusher.com/docs/channels/server_api/authenticating-users/#using-jsonp-in-pusher-js
// Route::post('/broadcast', function (Request $request) {
//     $pusher = new Pusher\Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'));

//     return $pusher->socket_auth($request->request->get('channel_name'), $request->request->get('socket_id'));
// });


Route::get('/tests', function () {
    $seeder = new TestsSeeder();
    $seeder->run();

    return response()->json('Success');
});