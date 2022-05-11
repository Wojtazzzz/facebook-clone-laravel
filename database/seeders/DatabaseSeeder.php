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
    private int $fakeUsersCount = 300;

    public function run()
    {
        User::truncate();
        Friendship::truncate();
        Poke::truncate();
        DB::table('notifications')->truncate();
        Message::truncate();
        Post::truncate();
        Like::truncate();
        Comment::truncate();

        User::factory(floor($this->fakeUsersCount))->create();
        Post::factory(floor($this->fakeUsersCount * 2))->create();
        Like::factory(floor($this->fakeUsersCount * 3))->create();
        Message::factory(floor($this->fakeUsersCount * 4))->create();
        Poke::factory(floor($this->fakeUsersCount / 3))->create();
        Comment::factory(1000)->create();

        $this->call([
            UserSeeder::class, // Root user
        ]);
    }
}
