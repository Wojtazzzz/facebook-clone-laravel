<?php

namespace App\Broadcasting;

use App\Models\Message;
use App\Models\User;

class ChatMessageChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @return array|bool
     */
    public function join(User $user, string $text, int $receiverId)
    {
        return $user->id === $receiverId
            || $user->id === Message::where([
                ['text', $text],
                ['sender_id', $user->id]
            ])->value('sender_id');
    }
}