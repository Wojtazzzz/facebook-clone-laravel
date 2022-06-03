<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(User $user, int $count)
    {
        Post::factory($count)->create([
            'author_id' => $user->id
        ]);
    }
}
