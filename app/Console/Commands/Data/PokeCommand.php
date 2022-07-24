<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Models\Poke;
use App\Models\User;
use Illuminate\Console\Command;

class PokeCommand extends Command
{
    protected $signature = 'data:poke {user} {amount=1} {--I|initiator}';
    protected $description = 'Create specific amount of pokes';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $user = User::findOrFail($this->argument('user'));

        if (!$this->checkAmount()) {
            return;
        }

        $amount = $this->argument('amount');
        $isInitiator = $this->option('initiator');

        Poke::factory($amount)->create([
            'friend_id' => $user->id,
        ] + ($isInitiator ? [
            'latest_initiator_id' => $user->id,
        ] : []));

        $this->info('Poke(s) created successfully.');
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
