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
}
