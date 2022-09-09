<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Models\Post;
use App\Models\User;
use App\Notifications\PostLiked;
use Illuminate\Console\Command;

class NotificationsCommand extends Command
{
    protected $signature = 'data:notification {user} {amount=1}';

    protected $description = 'Create notifications for specific user';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if (! $this->checkAmount()) {
            $this->error('Amount must be integer greater than 0.');

            return 1;
        }

        $user = User::findOrFail($this->argument('user'));
        $amount = $this->argument('amount');

        for ($i = 0; $i < $amount; $i++) {
            $friend = User::factory()->createOne();
            $post = Post::factory()->createOne();

            $user->notify(new PostLiked($friend->id, $post));
        }

        $this->info('Friendship(s) created successfully.');

        return 0;
    }

    private function checkAmount(): bool
    {
        if ((int) $this->argument('amount') >= 1) {
            return true;
        }

        return false;
    }
}
