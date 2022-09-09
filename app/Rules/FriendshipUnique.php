<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FriendshipUnique implements Rule
{
    public function passes($attribute, $value): bool
    {
        return ! Friendship::query()
            ->where(fn (Builder $query) => $query->relation(Auth::user()->id, $value, FriendshipStatus::PENDING))
            ->orWhere(fn (Builder $query) => $query->relation(Auth::user()->id, $value, FriendshipStatus::CONFIRMED))
            ->exists();
    }

    public function message(): string
    {
        return 'This relationship already exists.';
    }
}
