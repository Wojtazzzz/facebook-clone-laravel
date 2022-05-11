<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Comment $comment)
    {
        return $comment->author_id === $user->id;
    }
    
    public function delete(User $user, Comment $comment)
    {
        return $comment->author_id === $user->id;
    }
}
