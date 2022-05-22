<?php

namespace Database\Seeders;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;

class LikeSeeder extends Seeder
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    public function run(Post $post, Collection $usersIds, int $count)
    {
        Like::factory($count)
            ->randomUser($usersIds)
            ->create([
                'post_id' => $post->id
            ]);
    }
}
