<?php

declare(strict_types=1);

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\HiddenPostController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NextController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PokeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\SavedPostController;
use App\Http\Controllers\SuggestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::middleware('auth:sanctum')
    ->name('api.')
    ->group(function () {
        Route::controller(UserController::class)
            ->group(function () {
                Route::get('/user', 'user')->name('user');
                Route::get('/users', 'index')->name('search');
            });

        Route::controller(FriendController::class)
            ->name('friends.')
            ->prefix('/friends')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::delete('/{user}', 'destroy')->name('destroy');
            });

        Route::controller(InviteController::class)
            ->name('invites.')
            ->prefix('/invites')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/{user}', 'update')->name('update');
            });

        Route::name('suggests.')
            ->prefix('/suggests')
            ->group(function () {
                Route::get('/', SuggestController::class)->name('index');
            });

        Route::name('contacts.')
            ->prefix('/contacts')
            ->group(function () {
                Route::get('/', ContactController::class)->name('index');
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
                Route::put('/', 'update')->name('update');
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

                Route::put('/{post}/turn-off-comments', 'turnOffComments')->name('turnOffComments');
                Route::put('/{post}/turn-on-comments', 'turnOnComments')->name('turnOnComments');
            });

        Route::controller(PostLikeController::class)
            ->name('posts.likes.')
            ->prefix('/posts/{post}/likes')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/', 'destroy')->name('destroy');
            });

        Route::controller(PostController::class)
            ->name('users.posts.')
            ->prefix('/users/{user}/posts')
            ->group(function () {
                Route::get('/', 'userPosts')->name('index');
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
