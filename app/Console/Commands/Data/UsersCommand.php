<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Models\User;
use Illuminate\Console\Command;

class UsersCommand extends Command
{
    protected $signature = 'data:users {amount=1}';
    protected $description = 'Create specific amount of users';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        if (!$this->checkAmount()) {
            return;
        }

        $amount = $this->argument('amount');

        User::factory($amount)->create();

        $this->info('User(s) created successfully.');
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
