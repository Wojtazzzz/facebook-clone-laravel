<?php

namespace App\Rules;

use App\Models\Friendship;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class FriendshipUnique implements Rule
{
    public function passes($attribute, $value)
    {
        return !Friendship::where([
            ['user_id', Auth::user()->id],
            ['friend_id', $value],
        ])
        ->orWhere([
            ['user_id', $value],
            ['friend_id', Auth::user()->id],
        ])
        ->exists();
    }

    public function message()
    {
        return 'This relationship already exists.';
    }
}
