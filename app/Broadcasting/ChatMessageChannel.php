<?php

namespace App\Broadcasting;

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
    public function join(User $user, int $senderId, int $receiverId)
    {
        return $user->id === $receiverId || $user->id === $senderId;
    }
}