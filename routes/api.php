<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FriendshipController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PokeController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->name('api.')
    ->group(function () {
        Route::controller(UserController::class)
            ->group(function () {
                Route::get('/user', 'user')->name('user');
            });

        Route::controller(FriendshipController::class)
            ->name('friendship.')
            ->prefix('/friendship')
            ->group(function () {
                Route::get('/friends/{user}', 'friends')->name('friends');
                Route::get('/suggests', 'suggests')->name('suggests');
                Route::get('/invites', 'invites')->name('invites');

                Route::post('/invite', 'invite')->name('invite');
                Route::post('/accept', 'accept')->name('accept');
                Route::post('/reject', 'reject')->name('reject');
                Route::post('/destroy', 'destroy')->name('destroy');
            });

        Route::controller(PokeController::class)
            ->name('pokes.')
            ->prefix('/pokes')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'poke')->name('poke');
            });

        Route::controller(NotificationController::class)
            ->name('notifications.')
            ->prefix('/notifications')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/mark-as-read', 'markAsRead')->name('markAsRead');
            });

        Route::controller(MessageController::class)
            ->name('messages.')
            ->prefix('/messages')
            ->group(function () {
                Route::get('/{receiverId}', 'index')->whereNumber('receiverId')->name('index');
                Route::post('/', 'store')->name('store');
                Route::get('/messenger', 'messenger')->name('messenger');
            });

        Route::controller(PostController::class)
            ->name('posts.')
            ->prefix('/posts')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{post}', 'destroy')->name('destroy');
            });

        Route::controller(LikeController::class)
            ->name('likes.')
            ->prefix('/likes')
            ->group(function () {
                Route::post('/', 'store')->name('store');
                Route::delete('/{post}', 'destroy')->name('destroy');
            });

        Route::controller(CommentController::class)
            ->name('comments.')
            ->group(function () {
                Route::prefix('/posts/{resourceId}')
                    ->name('posts.')
                    ->group(function () {
                        Route::get('/comments', 'index')->name('index');
                        Route::post('/comments', 'store')->name('store');
                        Route::put('/comments/{comment}', 'update')->name('update');
                        Route::delete('/comments/{comment}', 'destroy')->name('destroy');
                    });
            });

        Broadcast::routes();
    });

Route::get('/users', [UserController::class, 'index'])->name('api.next.users');
Route::get('/users/{user}', [UserController::class, 'show'])->name('api.next.user');
