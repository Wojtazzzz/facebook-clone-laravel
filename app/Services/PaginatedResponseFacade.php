<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Facade;

class PaginatedResponseFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PaginatedResponse::class;
    }
}
