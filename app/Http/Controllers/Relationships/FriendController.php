<?php

declare(strict_types=1);

namespace App\Http\Controllers\Relationships;

use App\Http\Controllers\Controller;
use App\Http\Resources\FriendResource;
use App\Models\Friendship;
use App\Models\User;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FriendController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $pagination = $user->friends->paginate(10);

        return PaginatedResponseFacade::response(FriendResource::class, $pagination);
    }

    public function destroy(Request $request, User $user): Response
    {
        Friendship::query()
            ->relation($request->user()->id, $user->id)
            ->firstOrFail()
            ->delete();

        return response()->noContent();
    }
}
