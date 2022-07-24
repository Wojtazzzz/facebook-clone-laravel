<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Models\Message;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\WithFaker;

class Conversation extends Command
{
    use WithFaker;

    protected $signature = 'data:conversation {user : ID of first user} {friend : ID of second user} {amount : Amount of messages}';
    protected $description = 'Create messages between specified users';

    public function __construct()
    {
        parent::__construct();

        $this->setUpFaker();
    }

    public function handle(): void
    {
        if (!$this->checkAmount()) {
            return;
        }

        if (!$this->checkUsers()) {
            return;
        }

        $user = User::findOrFail($this->argument('user'));
        $friend = User::findOrFail($this->argument('friend'));
        $amount = $this->argument('amount');

        $faker = $this->faker;

        Message::factory($amount)->create(function () use ($user, $friend, $faker) {
            $sender = $faker->randomElement([$user, $friend]);

            return [
                'sender_id' => $sender->id,
                'receiver_id' => $sender->is($user)
                    ? $friend->id
                    : $user->id,
            ];
        });

        $this->info('Conversation created successfully.');
    }

    private function checkAmount(): bool
    {
        if ((int) $this->argument('amount') >= 1) {
            return true;
        }

        $this->error('Amount must be integer greater than 0.');

        return false;
    }

    private function checkUsers(): bool
    {
        if ($this->argument('user') !== $this->argument('friend')) {
            return true;
        }

        $this->error('Cannot pass same user twice.');

        return false;
    }
}
