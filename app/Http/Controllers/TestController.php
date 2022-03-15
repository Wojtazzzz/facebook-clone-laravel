<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;

class TestController extends Controller
{
    public function __invoke()
    {
        $user = User::findOrFail(51);
        $receiverId = 1;
        
        $messages = Message::where([
            ['sender_id', $user->id],
            ['receiver_id', $receiverId]
        ])->orWhere([
            ['sender_id', $receiverId],
            ['receiver_id', $user->id],
        ])
        ->latest()
        ->paginate(15, [
            'id', 
            'text', 
            'sender_id', 
            'receiver_id', 
            'created_at'
        ]);
    
        return MessageResource::collection($messages);
    }
}