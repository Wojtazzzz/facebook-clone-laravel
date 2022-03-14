<?php

namespace App\Http\Controllers;

use App\Http\Resources\FriendshipResource;
use App\Http\Resources\PokeResource;
use App\Http\Resources\UserResource;
use App\Models\Friendship;
use App\Models\Poke;
use App\Models\User;

class TestController extends Controller
{
    public function __invoke()
    {   
        $pokes = Poke::with('initiator')
            ->where('poked_id', 51)
            ->get([
                'id',
                'initiator_id',
                'count',
                'updated_at'
            ]);

        return PokeResource::collection($pokes);
    }
}