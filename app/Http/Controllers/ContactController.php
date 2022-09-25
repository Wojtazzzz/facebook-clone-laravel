<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $pagination = $user->friends->paginate(20);

        return PaginatedResponseFacade::response(ContactResource::class, $pagination);
    }
}
