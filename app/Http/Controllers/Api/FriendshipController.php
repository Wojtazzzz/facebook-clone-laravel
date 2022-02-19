<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Friendship\AcceptRequest;
use App\Http\Requests\Friendship\InviteRequest;
use App\Http\Requests\Friendship\RejectRequest;
use App\Models\Friendship;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class FriendshipController extends Controller
{
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

        return response(status: 201);
    }

    public function accept(AcceptRequest $request): Response | ResponseFactory
    {
        $data = $request->validated();

        Friendship::where([
            ['first_user', $data['user_id']],
            ['second_user', $request->user()->id]
        ])->update([
            'status' => 'confirmed'
        ]);

        return response(status: 200);
    }

    public function reject(RejectRequest $request): Response | ResponseFactory
    {
        $data = $request->validated();

        Friendship::where([
            ['first_user', $data['user_id']],
            ['second_user', $request->user()->id]
        ])->update([
            'status' => 'blocked'
        ]);

        return response(status: 200);
    }
}
