<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Saved;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class NotSaved implements Rule
{
    public function passes($attribute, $value): bool
    {
        return ! Saved::query()
            ->where('post_id', $value)
            ->where('user_id', Auth::user()->id)
            ->exists();
    }

    public function message(): string
    {
        return 'Cannot use on saved post.';
    }
}
