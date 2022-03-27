<?php

namespace App\Policies;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LikePolicy
{
    use HandlesAuthorization;

    public function create(User $user, Post $post): bool
    {
        return !!!Like::firstWhere([
            ['user_id', $user->id],
            ['post_id', $post->id]
        ]);
    }

    public function delete(User $user, Post $post): bool
    {
        return !!Like::firstWhere([
            ['user_id', $user->id],
            ['post_id', $post->id]
        ]);
    }
}
