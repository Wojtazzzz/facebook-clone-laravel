<?php

namespace App\Enums;

enum PostType: string
{
    case FRIEND = 'FRIEND';
    case HIDDEN = 'HIDDEN';
    case SAVED = 'SAVED';
    case OWN = 'OWN';
}
