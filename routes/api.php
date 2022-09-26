<?php

declare(strict_types=1);

use App\Http\Controllers\BirthdayController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NextController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PokeController;
use App\Http\Controllers\Posts\HiddenPostController;
use App\Http\Controllers\Posts\PostCommentController;
use App\Http\Controllers\Posts\PostController;
use App\Http\Controllers\Posts\PostLikeController;
use App\Http\Controllers\Posts\SavedPostController;
use App\Http\Controllers\SuggestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserFriendController;
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

        Route::name('birthdays.')
            ->prefix('/birthdays')
            ->group(function () {
                Route::get('/', BirthdayController::class)->name('index');
            });

        Route::controller(FriendController::class)
            ->name('friends.')
            ->prefix('/friends')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::delete('/{user}', 'destroy')->name('destroy');
            });

        Route::controller(UserFriendController::class)
            ->name('users.')
            ->prefix('/users/{user}/friends')
            ->group(function () {
                Route::get('/getByCount', 'getByCount')->name('getByCount');
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
                Route::get('/checkUnread', 'checkUnread')->name('checkUnread');
            });

        Route::controller(MessageController::class)
            ->name('messages.')
            ->prefix('/messages')
            ->group(function () {
                Route::get('/', 'messenger')->name('messenger');
                Route::post('/', 'store')->middleware(['throttle:message'])->name('store');
                Route::get('/checkUnread', 'checkUnread')->name('checkUnread');
                Route::put('/{user}/update', 'update')->name('update');
                Route::get('/{user}', 'index')->name('index');
            });

        Route::controller(PostController::class)
            ->name('posts.')
            ->prefix('/posts')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                // it's method POST because of it: https://github.com/laravel/framework/issues/13457
                Route::post('/{post}', 'update')->name('update');
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

        Route::controller(PostCommentController::class)
            ->name('posts.')
            ->group(function () {
                Route::prefix('/posts/{post}/comments')
                    ->name('comments.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::post('/', 'store')->name('store');
                        Route::put('/{comment}', 'update')->name('update');
                        Route::delete('/{comment}', 'destroy')->name('destroy');
                    });
            });

        Route::controller(CommentLikeController::class)
            ->name('comments.likes.')
            ->prefix('/comments/{comment}/likes')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/', 'destroy')->name('destroy');
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
