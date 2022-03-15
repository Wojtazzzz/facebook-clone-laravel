<?php

namespace App\Http\Controllers;

use App\Http\Resources\FriendshipResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\PokeResource;
use App\Http\Resources\UserResource;
use App\Models\Friendship;
use App\Models\Message;
use App\Models\Poke;
use App\Models\User;

class TestController extends Controller
{
    public function __invoke()
    {
        $user = User::findOrFail(51);

        $messages = Message::select(['receiver_id', 'sender_id', 'text', 'created_at'])
            ->distinct(['receiver_id', 'sender_id'])
            // ->latest()
            ->orderBy('receiver_id')
            ->get();

        return $messages;

        $messages = Message::distinct('receiver_id')
            ->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('receiver_id')
            ->get();
    
        return $messages;
    }
}