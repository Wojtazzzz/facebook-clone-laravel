<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Http\Resources\FriendResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!isset($user)) {
            $user = $request->user();
        }

        $user->load(['invitedFriends', 'invitedByFriends']);

        $friends = collect([
            ...$user->invitedFriends,
            ...$user->invitedByFriends,
        ])->paginate(10);

        return response()->json(FriendResource::collection($friends));
    }

    public function suggests(Request $request): JsonResponse
    {
        $user = $request->user();

        $users = User::whereNotIn('id', [
            $user->id,
            ...$user->invitedFriends->pluck('id'),
            ...$user->invitedByFriends->pluck('id'),
            ...$user->receivedInvites->pluck('id'),
            ...$user->sendedInvites->pluck('id'),
            ...$user->receivedBlocks->pluck('id'),
            ...$user->sendedBlocks->pluck('id'),
        ])->paginate(10);

        return response()->json(FriendResource::collection($users));
    }

    public function invites(Request $request): JsonResponse
    {
        $user = $request->user()->load('receivedInvites');
        $users = $user->receivedInvites;

        return response()->json(FriendResource::collection($users->paginate(10)));
    }

    public function contacts(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->loadMissing(['invitedFriends', 'invitedByFriends']);

        $friends = collect([
            ...$user->invitedFriends,
            ...$user->invitedByFriends,
        ])->paginate(10);

        return response()->json(ContactResource::collection($friends));
    }
}
