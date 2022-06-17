<?php

declare(strict_types=1);

namespace App\Broadcasting;

use App\Models\User;

class ChatMessageChannel
{
    public function join(User $user, int $senderId, int $receiverId): bool
    {
        return (bool) $user->id === $receiverId || $user->id === $senderId;
    }
}
