<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use App\Observers\PostObserver;
use App\Observers\UserObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
    ];

    protected $observers = [
        Post::class => [PostObserver::class],
        User::class => [UserObserver::class],
    ];

    public function boot()
    {
    }
}
