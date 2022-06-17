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
            ->where('status', FriendshipStatus::CONFIRMED)
            ->where([
                ['user_id', Auth::user()->id],
                ['friend_id', $value],
            ])
            ->orWhere([
                ['user_id', $value],
                ['friend_id', Auth::user()->id],
            ])
            ->exists();
    }

    public function message(): string
    {
        return 'This user is not your friend.';
    }
}
