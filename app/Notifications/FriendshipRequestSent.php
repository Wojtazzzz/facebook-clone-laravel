<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FriendshipRequestSent extends Notification
{
    use Queueable;

    private int $friendId;

    public function __construct(int $friendId)
    {
        $this->friendId = $friendId;
    }

    public function via(): array
    {
        return ['database'];
    }

    public function toArray(): array
    {
        return [
            'friendId' => $this->friendId,
            'message' => 'Sent you a friendship invitation',
            'link' => '/friends/invites',
        ];
    }
}
