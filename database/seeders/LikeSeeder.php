<?php

namespace Database\Seeders;

use App\Models\Like;
use App\Models\Post;
<<<<<<< HEAD
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
=======
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7

class LikeSeeder extends Seeder
{
    use WithFaker;

    public function __construct()
    {
<<<<<<< HEAD
    }
    
    public function run(User $user, int $count)
    {
        $posts = Post::pluck('id');

        Like::factory($count)->create([
            'user_id' => $user->id,
            'post_id' => fn () => $this->faker->unique->randomElement($posts)
        ]);
    }
=======
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
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
