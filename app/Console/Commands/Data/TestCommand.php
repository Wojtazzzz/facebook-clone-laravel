<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Enums\FriendshipStatus;
use App\Models\Comment;
use App\Models\Friendship;
use App\Models\Message;
use App\Models\Post;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'data:test';
    protected $description = 'Test command for experiments';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        // Friendship::factory(10)->create([
        //     'user_id' => 1,
        //     'status' => FriendshipStatus::CONFIRMED
        // ])->each(function (Friendship $friendship) {
        //     Post::factory(3)->create([
        //         'author_id' => $friendship->friend_id
        //     ])->each(function (Post $post) {
        //         Comment::factory(22)->create([
        //             'resource_id' => $post->id
        //         ]);
        //     });
        // });

        Friendship::factory(30)->create([
            'friend_id' => 1,
            'status' => FriendshipStatus::PENDING
        ]);
    }
}
