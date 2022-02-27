<?php

namespace App\Providers;

use App\Events\ChatMessageSended;
use App\Listeners\UpdateChatsAboutNewMessage;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // ChatMessageSended::class => [
        //     UpdateChatsAboutNewMessage::class
        // ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
