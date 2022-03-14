<?php

namespace App\Http\Controllers;

use App\Http\Resources\FriendshipResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PokeResource;
use App\Http\Resources\UserResource;
use App\Models\Friendship;
use App\Models\Poke;
use App\Models\User;

class TestController extends Controller
{
    public function __invoke()
    {
        $user = User::findOrFail(51);

        return NotificationResource::collection($user->notifications);
    }
}