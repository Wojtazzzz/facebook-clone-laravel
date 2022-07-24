<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;

class NotificationCommand extends Command
{
    protected $signature = 'data:notification {user} {amount=1}';
    protected $description = 'Create specific amount of notifications';

    public function handle(): void
    {
        $user = User::findOrFail($this->argument('user'));

        if (!$this->checkAmount()) {
            return;
        }

        $amount = $this->argument('amount');

        Notification::factory($amount)->create([
            'notifiable_id' => $user->id,
        ]);

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
}
