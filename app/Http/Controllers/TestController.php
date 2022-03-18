<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;

class TestController extends Controller
{
    public function __invoke()
    {
        $user = User::findOrFail(51);

        $messages = Message::where('sender_id', 94)
            ->orWhere('receiver_id', 94)->count();

        return $messages;
    }
}