<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
<<<<<<< HEAD
<<<<<<< HEAD

class PostSeeder extends Seeder
{
    public function run(User $user, int $count)
    {
        Post::factory($count)->create([
            'author_id' => $user->id
        ]);
=======
=======
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
use Illuminate\Foundation\Testing\WithFaker;

class PostSeeder extends Seeder
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    public function run(User $user, int $count, int $commentsCount)
    {
        Post::factory($count)
            ->create(['author_id' => $user->id])
            ->each(function (Post $post) use ($commentsCount) {
                $this->call(LikeSeeder::class, true, [
                    'post' => $post,
                    'usersIds' => User::pluck('id'),
                    'count' => $commentsCount
                ]);
            });
<<<<<<< HEAD
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
=======
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
    }
}
