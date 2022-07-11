<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class Friend implements Rule
{
    public function passes($attribute, $value): bool
    {
        return Friendship::query()
            ->relation(Auth::user()->id, $value)
            ->where('status', FriendshipStatus::CONFIRMED)
            ->exists();
    }

    public function message(): string
    {
        return 'This user is not your friend.';
    }
}
