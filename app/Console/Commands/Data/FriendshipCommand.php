<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Console\Command;

class FriendshipCommand extends Command
{
    protected $signature = 'data:friendship {user} {amount=1} {--S|status=confirmed}';

    protected $description = 'Create friendships for specific user';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->checkAmount()) {
            $this->error('Amount must be integer greater than 0.');

            return 1;
        }

        $user = User::findOrFail($this->argument('user'));
        $amount = $this->argument('amount');

        $status = $this->getStatus();
        $column = $this->getColumn($status);

        Friendship::factory($amount)->create([
            $column => $user->id,
            'status' => $status,
        ]);

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

    private function getStatus(): FriendshipStatus
    {
        return match ($this->option('status')) {
            'pending' => FriendshipStatus::PENDING,
            'blocked' => FriendshipStatus::BLOCKED,
            default => FriendshipStatus::CONFIRMED,
        };
    }

    private function getColumn(FriendshipStatus $status): string
    {
        return FriendshipStatus::PENDING === $status ? 'friend_id' : 'user_id';
    }
}
