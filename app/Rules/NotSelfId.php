<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class NotSelfId implements Rule
{
    public function passes($attribute, $value): bool
    {
        return $value !== Auth::user()->id;
    }

    public function message(): string
    {
        return 'Cannot use this action on self.';
    }
}
