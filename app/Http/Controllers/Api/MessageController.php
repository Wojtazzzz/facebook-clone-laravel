<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request, int $receiverId): JsonResponse
    {
        $messages = Message::where([
            ['sender_id', $request->user()->id],
            ['receiver_id', $receiverId],
        ])->orWhere([
            ['sender_id', $receiverId],
            ['receiver_id', $request->user()->id],
        ])
        ->latest()
        ->paginate(15, [
            'id',
            'text',
            'sender_id',
            'created_at',
        ]);

        return response()->json(MessageResource::collection($messages));
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $message = Message::create($request->validated() + [
            'sender_id' => $request->user()->id,
        ]);

        return response()->json(new MessageResource($message), 201);
    }

    public function messenger(Request $request): JsonResponse
    {
        $user = $request->user();

        $friends = User::query()
            ->whereHas('invitedByFriends', fn(Builder $query) => $query
                ->where('user_id', $user->id)
                ->orWhere('friend_id', $user->id)
            )
            ->orWhereHas('invitedFriends', fn(Builder $query) => $query
                ->where('user_id', $user->id)
                ->orWhere('friend_id', $user->id)
            )
            ->inRandomOrder()
            ->paginate(10);

        return response()->json(UserResource::collection($friends));
    }
}
