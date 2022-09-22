<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserFriendResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserFriendController extends Controller
{
    public function getByCount(Request $request, User $user): JsonResponse
    {
        $countToFetch = (int) $request->query('count', 0);

        $friends = $user->friends->random(min($countToFetch, $user->friends->count()));

        return response()->json(UserFriendResource::collection($friends));
    }
}
