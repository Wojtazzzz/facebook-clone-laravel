<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FriendshipController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PokeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PostController;
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
            ->prefix('/pokes')
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::post('/{poke}', 'update');
            });

        Route::controller(NotificationController::class)
            ->prefix('/notifications')
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/mark-as-read', 'markAsRead');
            });

        Route::controller(MessageController::class)
            ->prefix('/messages')
            ->group(function () {
                Route::get('/{receiverId}', 'index')->whereNumber('receiverId');
                Route::post('/', 'store');
                Route::get('/messenger', 'messenger');
            });

        Route::controller(PostController::class)
            ->prefix('/posts')
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::delete('/{post}', 'destroy');
            });

        Route::controller(LikeController::class)
            ->prefix('/likes')
            ->group(function () {
                Route::post('/', 'store');
                Route::delete('/{post}', 'destroy');
            });

        Route::controller(CommentController::class)
            ->group(function () {
                Route::get('/posts/{resourceId}/comments', 'index');
                Route::post('/posts/{resourceId}/comments', 'store');
                Route::put('/posts/{resourceId}/comments/{comment}', 'update');
                Route::delete('/posts/{resourceId}/comments/{comment}', 'destroy');
            });

        Broadcast::routes();
    });

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);