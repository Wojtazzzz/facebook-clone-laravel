<?php

declare(strict_types=1);

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\HiddenPostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NextController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PokeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SavedPostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->name('api.')
    ->group(function () {
        Route::controller(UserController::class)
            ->group(function () {
                Route::get('/user', 'user')->name('user');
            });

        Route::controller(FriendController::class)
            ->name('friends.')
            ->prefix('/friends')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/suggests', 'suggests')->name('suggests');
                Route::get('/invites', 'invites')->name('invites');
                Route::get('/contacts', 'contacts')->name('contacts');
            });

        Route::controller(FriendshipController::class)
            ->name('friendship.')
            ->prefix('/friendship')
            ->group(function () {
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
                Route::put('/mark-as-read', 'markAsRead')->name('markAsRead');
            });

        Route::controller(MessageController::class)
            ->name('messages.')
            ->prefix('/messages')
            ->group(function () {
                Route::get('/', 'messenger')->name('messenger');
                Route::post('/', 'store')->name('store');
                Route::get('/{user}', 'index')->name('index');
            });

        Route::controller(PostController::class)
            ->name('posts.')
            ->prefix('/posts')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{post}', 'destroy')->name('destroy');
            });

        Route::name('hidden.')
            ->prefix('/hidden')
            ->group(function () {
                Route::controller(HiddenPostController::class)
                    ->name('posts.')
                    ->prefix('/posts')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::post('/', 'store')->name('store');
                        Route::delete('/{post}', 'destroy')->name('destroy');
                    });
            });

        Route::name('saved.')
            ->prefix('/saved')
            ->group(function () {
                Route::controller(SavedPostController::class)
                    ->name('posts.')
                    ->prefix('/posts')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::post('/', 'store')->name('store');
                        Route::delete('/{post}', 'destroy')->name('destroy');
                    });
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
                Route::prefix('/posts/{resourceId}/comments')
                    ->name('posts.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::post('/', 'store')->name('store');
                        Route::put('/{comment}', 'update')->name('update');
                        Route::delete('/{comment}', 'destroy')->name('destroy');
                    });
            });

        Broadcast::routes();

        Route::controller(NextController::class)
            ->withoutMiddleware('auth:sanctum')
            ->name('next.')
            ->prefix('/next')
            ->group(function () {
                Route::get('/profiles/{user}', 'profile')->name('profile');
                Route::get('/profiles', 'profiles')->name('profiles');
            });
    });
