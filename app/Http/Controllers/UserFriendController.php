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

        $friendsCount = $user->friends->count();
        $friends = $user->friends->random(min($countToFetch, $friendsCount));

        return response()->json([
            'friends' => UserFriendResource::collection($friends),
            'count' => $friendsCount,
        ]);
    }
}
