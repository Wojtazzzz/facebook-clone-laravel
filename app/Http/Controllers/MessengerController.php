<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserFriendResource;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessengerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $pagination = $user->friends->paginate(15);

        return PaginatedResponseFacade::response(UserFriendResource::class, $pagination);
    }

    public function checkUnread(Request $request): JsonResponse
    {
        $exist = $request->user()
            ->receivedMessages()
            ->whereNull('read_at')
            ->exists();

        return response()->json((bool) $exist);
    }
}
