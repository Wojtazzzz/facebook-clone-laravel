<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Models\User;
use App\Notifications\FriendshipRequestAccepted;
use App\Notifications\FriendshipRequestSent;
use App\Notifications\Poked;
use App\Notifications\PostLiked;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notification as NotificationsNotification;

class NotifyCommand extends Command
{
    protected $signature = 'data:notify {user} {friend} {type} {amount=1}';

    protected $description = 'Create specific amount of notifications';

    public function handle(): void
    {
        $user = User::findOrFail($this->argument('user'));
        $friend = User::query()
            ->where(['id' => $this->argument('friend')])
            ->firstOr(fn () => User::factory()->createOne());

        if (! $this->checkAmount()) {
            return;
        }

        $amount = $this->argument('amount');

        $type = $this->getType($friend);

        for ($i = 0; $i < $amount; $i++) {
            $user->notify($type);
        }

        $this->info('Notification(s) created successfully.');
    }

    private function checkAmount(): bool
    {
        if ((int) $this->argument('amount') >= 1) {
            return true;
        }

        $this->error('Amount must be integer greater than 0.');

        return false;
    }

    private function getType(User $friend): NotificationsNotification
    {
        return match ($this->argument('type')) {
            'poked' => new Poked($friend->id, rand(1, 999)),
            'invSent' => new FriendshipRequestSent($friend->id),
            'invAccepted' => new FriendshipRequestAccepted($friend->id),
            'postLiked' => new PostLiked($friend->id, rand(1, 999)),
        };
    }
}
