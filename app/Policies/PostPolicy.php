<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, Post $post): bool
    {
        return $post->author_id === $user->id;
    }

    public function update(User $user, Post $post): bool
    {
        return $post->author_id === $user->id;
    }

    public function commenting(User $user, Post $post): bool
    {
        return $post->author_id === $user->id;
    }
}
