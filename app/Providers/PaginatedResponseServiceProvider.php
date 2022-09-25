<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\PaginatedResponse;
use Illuminate\Support\ServiceProvider;

class PaginatedResponseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaginatedResponse::class, function () {
            return new PaginatedResponse();
        });
    }

    public function boot(): void
    {
        //
    }
}
