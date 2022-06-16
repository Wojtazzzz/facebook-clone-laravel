<?php

namespace App\Policies;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LikePolicy
{
    use HandlesAuthorization;

    public function delete(User $user, Post $post): bool
    {
        return (bool) Like::firstWhere([
            ['user_id', $user->id],
            ['post_id', $post->id],
        ]);
    }
}
