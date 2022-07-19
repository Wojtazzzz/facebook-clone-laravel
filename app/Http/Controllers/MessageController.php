<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
    // $user === $friend
    public function index(Request $request, User $user): JsonResponse
    {
        $messages = Message::query()
            ->conversation($request->user()->id, $user->id)
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
        $message = Message::create($request->validated());

        return response()->json(new MessageResource($message), 201);
    }

    public function messenger(Request $request): JsonResponse
    {
        $user = $request->user();

        $friends = User::query()
            ->whereNot('id', $user->id)
            ->where(fn (Builder $query) => $query
                ->whereHas('invitedByFriends', fn (Builder $query) => $query
                    ->where('user_id', $user->id)
                    ->orWhere('friend_id', $user->id)
                )
                ->orWhereHas('invitedFriends', fn (Builder $query) => $query
                    ->where('user_id', $user->id)
                    ->orWhere('friend_id', $user->id)
                )
            )
            ->paginate(10);

        return response()->json(UserResource::collection($friends));
    }
}
