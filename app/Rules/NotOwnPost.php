<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Post;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class NotOwnPost implements Rule
{
    public function passes($attribute, $value): bool
    {
        $post = Post::findOrFail($value);

        return $post->author_id !== Auth::user()->id;
    }

    public function message(): string
    {
        return 'Cannot use on own post.';
    }
}
