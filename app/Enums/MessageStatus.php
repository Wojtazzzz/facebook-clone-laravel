<?php

namespace App\Enums;

enum MessageStatus: string
{
    case SENDING = 'SENDING';
    case DELIVERED = 'DELIVERED';
    case READ = 'READ';
}
