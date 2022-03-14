<?php

namespace App\Rules;

use App\Models\Friendship;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class IsFriend implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Friendship::where([
            ['user_id', Auth::user()->id],
            ['friend_id', $value],
        ])->orWhere([
            ['user_id', $value],
            ['friend_id', Auth::user()->id],
        ])->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This user is not your friend.';
    }
}
