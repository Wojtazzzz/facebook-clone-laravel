<?php

declare(strict_types=1);

namespace App\Http\Controllers\Relationships;

use App\Enums\FriendshipStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Friendship\Invite\StoreRequest;
use App\Http\Requests\Friendship\Invite\UpdateRequest;
use App\Http\Resources\FriendResource;
use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendshipRequestAccepted;
use App\Notifications\FriendshipRequestSent;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InviteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->load('receivedInvites');
        $pagination = $user->receivedInvites->paginate(10);

        return PaginatedResponseFacade::response(FriendResource::class, $pagination);
    }

    public function store(StoreRequest $request): Response
    {
        $data = $request->validated();
        $userId = $request->user()->id;

        $friend = User::findOrFail($data['friend_id']);

        Friendship::create([
            'user_id' => $userId,
            'friend_id' => $data['friend_id'],
            'status' => FriendshipStatus::PENDING,
        ]);

        $friend->notify(new FriendshipRequestSent($userId));

        return response(status: 201);
    }

    public function update(UpdateRequest $request, User $user): Response
    {
        $data = $request->validated();

        Friendship::query()
            ->where([
                ['user_id', $user->id],
                ['friend_id', $request->user()->id],
            ])
            ->where('status', FriendshipStatus::PENDING)
            ->firstOrFail()
            ->update([
                'status' => $data['status'],
            ]);

        if ($data['status'] === FriendshipStatus::CONFIRMED->value) {
            $user->notify(new FriendshipRequestAccepted($request->user()->id));
        }

        return response()->noContent();
    }
}
