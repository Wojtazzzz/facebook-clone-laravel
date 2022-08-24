<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Like;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class LikeNotExists implements Rule
{
    public function passes($attribute, $value): bool
    {
        return ! (bool) Like::firstWhere([
            ['user_id', Auth::user()->id],
            ['post_id', $value],
        ]);
    }

    public function message(): string
    {
        return 'You already like this post.';
    }
}
