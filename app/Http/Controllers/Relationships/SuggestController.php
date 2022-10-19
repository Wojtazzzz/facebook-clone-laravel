<?php

declare(strict_types=1);

namespace App\Http\Controllers\Relationships;

use App\Http\Controllers\Controller;
use App\Http\Resources\FriendResource;
use App\Models\User;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuggestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $pagination = User::whereNotIn('id', [
            $user->id,
            ...$user->friends->pluck('id'),
            ...$user->receivedInvites->pluck('id'),
            ...$user->sendedInvites->pluck('id'),
            ...$user->receivedBlocks->pluck('id'),
            ...$user->sendedBlocks->pluck('id'),
        ])->paginate(10);

        return PaginatedResponseFacade::response(FriendResource::class, $pagination);
    }
}
