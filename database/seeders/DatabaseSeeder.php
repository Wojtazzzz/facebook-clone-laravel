<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Friendship;
use App\Models\Like;
use App\Models\Message;
use App\Models\Poke;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->clearDatabase();

        $users = User::factory(200)->create();

        Post::factory(1000)
            ->create()
            ->each(function (Post $post) use ($users) {
                Like::factory(rand(5, 10))
                    ->randomUser($users->pluck('id'))
                    ->create([
                        'post_id' => $post->id
                    ]);
            });

        Message::factory(5000)->create();
        // Poke::factory(500)->create();
        // Comment::factory(1000)->create();

        $this->call([
            UserSeeder::class
        ]);
    }

    private function clearDatabase()
    {
        User::truncate();
        Post::truncate();
        Like::truncate();
        Message::truncate();

        Poke::truncate();
        Friendship::truncate();
        DB::table('notifications')->truncate();
        Comment::truncate();
    }
}
