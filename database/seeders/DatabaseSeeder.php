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
<<<<<<< HEAD

        User::factory(2000)->create();
        Post::factory(2000)->create();
        Like::factory(500)->create();
        Comment::factory(3000)->create();
        Friendship::factory(400)->create();

        $user = User::factory()->createOne([
            'first_name' => 'Marcin',
            'last_name' => 'Witas',
            'email' => 'marcin.witas72@gmail.com',
        ]);

        $this->call(PostSeeder::class, parameters: [
            'user' => $user,
            'count' => 30
        ]);

        $this->call(LikeSeeder::class, parameters: [
            'user' => $user,
            'count' => 100
        ]);

        $this->call(CommentSeeder::class, parameters: [
            'user' => $user,
            'count' => 200
        ]);

        $this->call(FriendshipSeeder::class, parameters: [
            'user' => $user,
            'count' => 100
        ]);

        $this->call(MessageSeeder::class, parameters: [
            'user' => $user,
            'count' => 1000
        ]);

        $this->call(PokeSeeder::class, parameters: [
            'user' => $user,
            'count' => 20
        ]);
    }

    private function clearDatabase()
    {
        User::truncate();
        Post::truncate();
        Like::truncate();
        Message::truncate();
        Poke::truncate();
        Comment::truncate();
        Friendship::truncate();

        DB::table('notifications')->truncate();
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
=======

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
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
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
