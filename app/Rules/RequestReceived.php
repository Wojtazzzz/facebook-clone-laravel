<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RequestReceived implements Rule
{
    private FriendshipStatus $status;

    public function __construct(FriendshipStatus $status = FriendshipStatus::PENDING)
    {
        $this->status = $status;
    }

    public function passes($attribute, $value): bool
    {
        return Friendship::where([
            'user_id' => $value,
            'friend_id' => Auth::user()->id,
        ])
        ->where('status', $this->status)
        ->exists();
    }

    public function message(): string
    {
        return 'This Friendship instance not exists.';
    }
}
