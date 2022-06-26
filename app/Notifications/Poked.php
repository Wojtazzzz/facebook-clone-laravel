<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class Poked extends Notification
{
    use Queueable;

    private int $userId;
    private int $pokesCount;

    public function __construct(int $userId, int $pokesCount)
    {
        $this->userId = $userId;
        $this->pokesCount = $pokesCount;
    }

    public function via(): array
    {
        return ['database'];
    }

    public function toArray(): array
    {
        return [
            'friendId' => $this->userId,
            'message' => $this->getMessage(),
            'link' => '/friends/pokes',
        ];
    }

    private function getMessage(): string
    {
        return 1 === $this->pokesCount
            ? 'Poked you first time'
            : "Poked you $this->pokesCount times in a row";
    }
}
