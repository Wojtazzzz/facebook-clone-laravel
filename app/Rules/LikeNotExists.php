<?php

namespace App\Rules;

use App\Models\Like;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class LikeNotExists implements Rule
{
    public function passes($attribute, $value)
    {
        return !(bool) Like::firstWhere([
            ['user_id', Auth::user()->id],
            ['post_id', $value],
        ]);
    }

    public function message()
    {
        return 'You already like this post.';
    }
}
