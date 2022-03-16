<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;

class TestController extends Controller
{
    public function __invoke()
    {
        $user = User::findOrFail(51);

        $messages = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('receiver_id')
            ->get();

        $messages->unique(['receiver_id', 'sender_id']);

        return $messages;
    }
}