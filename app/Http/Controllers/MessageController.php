<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MessageStatus;
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
        $pagination = Message::query()
            ->conversation($request->user()->id, $user->id)
            ->latest()
            ->paginate(15, [
                'id',
                'content',
                'sender_id',
                'status',
                'read_at',
                'created_at',
            ]);

        return response()->json([
            'data' => MessageResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $message = Message::create($request->validated() + [
            'status' => MessageStatus::DELIVERED,
            'sender_id' => $request->user()->id,
        ]);

        return response()->json(new MessageResource($message), 201);
    }

    public function messenger(Request $request): JsonResponse
    {
        $user = $request->user();

        $pagination = User::query()
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
            ->paginate(15);

        return response()->json([
            'data' => UserResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }
}
