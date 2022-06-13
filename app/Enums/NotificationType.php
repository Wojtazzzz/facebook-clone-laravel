<?php

namespace App\Enums;

enum NotificationType: string
{
    case FRIENDSHIP_REQUEST_SENT = 'FRIENDSHIP_REQUEST_SENT';
    case FRIENDSHIP_REQUEST_ACCEPTED = 'FRIENDSHIP_REQUEST_ACCEPTED';
}
