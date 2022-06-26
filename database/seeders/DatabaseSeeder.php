<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Friendship;
use App\Models\Like;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Poke;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->clearDatabase();

        User::factory(2000)->create();
        Post::factory(2000)->create();
        Like::factory(500)->create();
        Comment::factory(3000)->create();
        Friendship::factory(400)->create();
        Notification::factory(1000)->create();

        $user = User::factory()->createOne([
            'first_name' => 'Marcin',
            'last_name' => 'Witas',
            'email' => 'marcin.witas72@gmail.com',
        ]);

        // $user = User::firstWhere('last_name', 'Witas');

        $this->call(PostSeeder::class, parameters: [
            'user' => $user,
            'count' => 30,
        ]);

        $this->call(LikeSeeder::class, parameters: [
            'user' => $user,
            'count' => 100,
        ]);

        $this->call(CommentSeeder::class, parameters: [
            'user' => $user,
            'count' => 200,
        ]);

        $this->call(FriendshipSeeder::class, parameters: [
            'user' => $user,
            'count' => 80,
        ]);

        $this->call(MessageSeeder::class, parameters: [
            'user' => $user,
            'count' => 1000,
        ]);

        $this->call(PokeSeeder::class, parameters: [
            'user' => $user,
            'count' => 25,
        ]);

        $this->call(NotificationSeeder::class, parameters: [
            'user' => $user,
            'count' => 25,
        ]);
    }

    private function clearDatabase(): void
    {
        User::truncate();
        Post::truncate();
        Like::truncate();
        Message::truncate();
        Poke::truncate();
        Comment::truncate();
        Friendship::truncate();
        Notification::truncate();
    }
}
