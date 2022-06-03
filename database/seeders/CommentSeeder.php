<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(User $user, int $count)
    {
        Comment::factory($count)->create([
            'author_id' => $user->id
        ]);
    }
}
