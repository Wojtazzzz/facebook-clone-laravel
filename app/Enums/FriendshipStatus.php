<?php

declare(strict_types=1);

namespace App\Enums;

enum FriendshipStatus: string
{
    case CONFIRMED = 'CONFIRMED';
    case PENDING = 'PENDING';
    case BLOCKED = 'BLOCKED';
}
