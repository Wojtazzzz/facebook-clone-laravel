<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Console\Command;

class UserCommand extends Command
{
    protected $signature = 'data:user {--F|friend : Whether user should have friend}';

    protected $description = 'Create root user';

    private int $id = 1;

    private string $first_name = 'Marcin';

    private string $last_name = 'Witas';

    private string $email = 'marcin.witas72@gmail.com';

    public function handle(): void
    {
        $isExists = User::query()
            ->where('id', $this->id)
            ->orWhere('email', $this->email)
            ->exists();

        if ($isExists) {
            $this->error('User already exists.');

            return;
        }

        User::factory()->createOne([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
        ]);

        $this->info('User created successfully.');

        if (! $this->option('friend')) {
            return;
        }

        Friendship::factory()->createOne([
            'user_id' => $this->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $this->info('Friendship created successfully.');
    }
}
