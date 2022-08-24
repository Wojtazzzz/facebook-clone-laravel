<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\HiddenPost;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class NotHidden implements Rule
{
    public function passes($attribute, $value): bool
    {
        return ! HiddenPost::query()
            ->where('post_id', $value)
            ->where('user_id', Auth::user()->id)
            ->exists();
    }

    public function message(): string
    {
        return 'Cannot use on hidden post.';
    }
}
