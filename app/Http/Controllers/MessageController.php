<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MessageStatus;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserFriendResource;
use App\Http\Resources\UserResource;
use App\Models\Message;
use App\Models\User;
use App\Services\PaginatedResponseFacade;
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
                'images',
                'sender_id',
                'status',
                'read_at',
                'created_at',
            ]);

        return PaginatedResponseFacade::response(MessageResource::class, $pagination);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $paths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('messages', 'public');

                $paths[] = str_replace('public', '', $path);
            }
        }

        $request->user()
            ->sentMessages()
            ->create([
                'content' => $request->validated('content'),
                'images' => $paths,
                'receiver_id' => $request->validated('receiver_id'),
                'status' => MessageStatus::DELIVERED,
            ]);

        return response()->json(status: 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $request->user()
                ->receivedMessages()
                ->where('sender_id', $user->id)
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                    'status' => MessageStatus::READ,
                ]);

        return response()->json();
    }

    public function messenger(Request $request): JsonResponse
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
