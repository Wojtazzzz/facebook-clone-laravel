<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Friendship\AcceptRequest;
use App\Http\Requests\Friendship\DestroyRequest;
use App\Http\Requests\Friendship\InviteRequest;
use App\Http\Requests\Friendship\RejectRequest;
use App\Http\Resources\UserResource;
use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendshipInvitationSended;
use App\Notifications\FriendshipInvitationAccepted;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    // Load friends list
    public function friends(User $user): JsonResponse
    {
        $user->load(['invitedFriends', 'invitedByFriends']);

        $friends = collect([
            ...$user->invitedFriends,
            ...$user->invitedByFriends
        ])->paginate(10);

        return response()->json(UserResource::collection($friends));
    }

    // Load users which are suggests for send invitation
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
            
        return response()->json(UserResource::collection($users));
    }
        
    // Load users which send invitations to logged user
    public function invites(Request $request): JsonResponse
    {
        $user = $request->user()->load('receivedInvites');
        $users = $user->receivedInvites; 

        return response()->json(UserResource::collection($users->paginate(10)));
    }

    // Send invitation to user
    public function invite(InviteRequest $request): JsonResponse
    {
        $data = $request->validated();
        $friend = User::findOrFail($data['user_id']);

        Friendship::create([
            'user_id' => $request->user()->id,
            'friend_id' => $friend->id,
            'status' => 'pending'
        ]);

        $friend->notify(new FriendshipInvitationSended($request->user()));

        return response()->json(new UserResource($friend), 201);
    }

    // Accept invitation from another user
    public function accept(AcceptRequest $request): JsonResponse
    {
        $data = $request->validated();
        $friend = User::findOrFail($data['user_id']);

        Friendship::where([
            ['user_id', $friend->id],
            ['friend_id', $request->user()->id]
        ])->update([
            'status' => 'confirmed'
        ]);

        $friend->notify(new FriendshipInvitationAccepted($request->user()));

        return response()->json([
            'data' => new UserResource($friend),
            'message' => 'Request accepted successfully' 
        ], 201);
    }

    // Reject invitation from another user
    public function reject(RejectRequest $request): JsonResponse
    {
        $data = $request->validated();
        $friend = User::findOrFail($data['user_id']);

        Friendship::where([
            ['user_id', $friend->id],
            ['friend_id', $request->user()->id]
        ])->update([
            'status' => 'blocked'
        ]);

        return response()->json([
            'data' => new UserResource($friend),
            'message' => 'Request rejected successfully' 
        ], 201);
    }

    // Remove user from friends list
    public function destroy(DestroyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $friend = User::findOrFail($data['user_id']);

        Friendship::where([
            ['user_id', $friend->id],
            ['friend_id', $request->user()->id]
        ])->orWhere([
            ['user_id', $request->user()->id],
            ['friend_id', $friend->id]
        ])->delete();

        return response()->json(new UserResource($friend), 201);
    }
}
