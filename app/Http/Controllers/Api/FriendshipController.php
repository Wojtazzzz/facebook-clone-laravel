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
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class FriendshipController extends Controller
{
    // Load friends list
    public function friends(User $user): JsonResource
    {
        $user->load(['invitedFriends', 'invitedByFriends']);

        $friends = collect([
            ...$user->invitedFriends,
            ...$user->invitedByFriends
        ])->paginate(10);

        return UserResource::collection($friends);
    }

    // Load users which are suggests for send invitation
    public function suggests(Request $request): JsonResource
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
            
        return UserResource::collection($users);
    }
        
    // Load users which send invitations to logged user
    public function invites(Request $request): JsonResource
    {
        $user = $request->user()->load('receivedInvites');
        $users = $user->receivedInvites; 

        return UserResource::collection($users->paginate(10));
    }

    // Send invitation to user
    public function invite(InviteRequest $request): Response | ResponseFactory
    {
        $data = $request->validated();
        $inviter_id = $request->user()->id;

        Friendship::create([
            'first_user' => $inviter_id,
            'second_user' => $data['user_id'],
            'acted_user' => $inviter_id,
            'status' => 'pending'
        ]);

        User::find($data['user_id'])
            ->notify(new FriendshipInvitationSended($request->user()));

        return response(status: 201);
    }

    // Accept invitation from another user
    public function accept(AcceptRequest $request): Response | ResponseFactory
    {
        $data = $request->validated();

        Friendship::where([
            ['user_id', $data['user_id']],
            ['friend_id', $request->user()->id]
        ])->update([
            'status' => 'confirmed'
        ]);

        User::find($data['user_id'])
            ->notify(new FriendshipInvitationAccepted($request->user()));

        return response();
    }

    // Reject invitation from another user
    public function reject(RejectRequest $request): Response | ResponseFactory
    {
        $data = $request->validated();

        Friendship::where([
            ['user_id', $data['user_id']],
            ['friend_id', $request->user()->id]
        ])->update([
            'status' => 'blocked'
        ]);

        return response();
    }

    // Remove user from friends list
    public function destroy(DestroyRequest $request): Response | ResponseFactory
    {
        $data = $request->validated();

        Friendship::where([
            ['user_id', $data['user_id']],
            ['friend_id', $request->user()->id]
        ])->orWhere([
            ['user_id', $request->user()->id],
            ['friend_id', $data['user_id']]
        ])->delete();
        
        return response();
    }
}
