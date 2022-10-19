<?php

declare(strict_types=1);

use App\Http\Controllers\BirthdayController;
use App\Http\Controllers\CommentLikeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Relationships\FriendController;
use App\Http\Controllers\Relationships\InviteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessengerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PokeController;
use App\Http\Controllers\Posts\HiddenController;
use App\Http\Controllers\Posts\PostCommentController;
use App\Http\Controllers\Posts\PostCommentingController;
use App\Http\Controllers\Posts\PostController;
use App\Http\Controllers\Posts\PostLikeController;
use App\Http\Controllers\Posts\SavedController;
use App\Http\Controllers\Posts\UserPostController;
use App\Http\Controllers\Relationships\SuggestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Relationships\UserFriendController;
use App\Http\Controllers\SSGController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::middleware('auth:sanctum')
    ->name('api.')
    ->group(function () {
        Route::controller(UserController::class)
            ->name('users.')
            ->group(function () {
                Route::get('/users', 'index')->name('index');
                Route::get('/user', 'show')->name('show');
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

        Route::controller(SuggestController::class)
            ->name('suggests.')
            ->prefix('/suggests')
            ->group(function () {
                Route::get('/', 'index')->name('index');
            });

        Route::controller(PokeController::class)
            ->name('pokes.')
            ->prefix('/pokes')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'poke')->name('poke');
            });

        Route::controller(UserFriendController::class)
            ->name('users.friends.')
            ->prefix('/users/{user}/friends')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/get-by-count', 'getByCount')->name('getByCount');
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
            });

        Route::controller(UserPostController::class)
            ->name('users.posts.')
            ->prefix('/users/{user}/posts')
            ->group(function () {
                Route::get('/', 'index')->name('index');
            });

        Route::controller(PostLikeController::class)
            ->name('posts.likes.')
            ->prefix('/posts/{post}/likes')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/', 'destroy')->name('destroy');
            });

        Route::controller(PostCommentingController::class)
            ->name('posts.commenting.')
            ->prefix('/posts')
            ->group(function () {
                Route::put('/{post}/commenting', 'update')->name('update');
            });

        Route::controller(PostCommentController::class)
            ->name('posts.comments.')
            ->prefix('/posts/{post}/comments')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/{comment}', 'update')->name('update');
                Route::delete('/{comment}', 'destroy')->name('destroy');
            });

        Route::controller(CommentLikeController::class)
            ->name('comments.likes.')
            ->prefix('/comments/{comment}/likes')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/', 'destroy')->name('destroy');
            });

        Route::controller(HiddenController::class)
            ->name('hidden.')
            ->prefix('/hidden')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{post}', 'destroy')->name('destroy');
            });

        Route::controller(SavedController::class)
            ->name('saved.')
            ->prefix('/saved')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::delete('/{post}', 'destroy')->name('destroy');
            });

        Route::controller(NotificationController::class)
            ->name('notifications.')
            ->prefix('/notifications')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::put('/', 'update')->name('update');
                Route::get('/check-unread', 'checkUnread')->name('checkUnread');
            });

        Route::controller(MessengerController::class)
            ->name('messenger.')
            ->prefix('/messenger')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/check-unread', 'checkUnread')->name('checkUnread');
            });

        Route::controller(MessageController::class)
            ->name('messages.')
            ->prefix('/messages')
            ->group(function () {
                Route::get('/{user}', 'index')->name('index');
                Route::put('/{user}', 'update')->name('update');
                Route::post('/', 'store')->middleware(['throttle:message'])->name('store');
            });

        Route::controller(ContactController::class)
            ->name('contacts.')
            ->prefix('/contacts')
            ->group(function () {
                Route::get('/', 'index')->name('index');
            });

        Route::controller(BirthdayController::class)
            ->name('birthdays.')
            ->prefix('/birthdays')
            ->group(function () {
                Route::get('/', 'index')->name('index');
            });

        Route::controller(SSGController::class)
            ->name('ssg.')
            ->prefix('/ssg')
            ->withoutMiddleware('auth:sanctum')
            ->group(function () {
                Route::get('/profiles', 'index')->name('index');
                Route::get('/profiles/{user}', 'show')->name('show');
            });

        Broadcast::routes();
    });
