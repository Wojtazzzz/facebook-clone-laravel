<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Friendship\InviteRequest;
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
}
