<?php

declare(strict_types=1);

namespace App\Console\Commands\Data;

use App\Models\User as UserModel;
use Illuminate\Console\Command;

class User extends Command
{
    protected $signature = 'data:user';
    protected $description = 'Create fake user';

    private string $first_name = 'Marcin';
    private string $last_name = 'Witas';
    private string $email = 'marcin.witas72@gmail.com';

    public function handle(): void
    {
        UserModel::when(
            UserModel::where('email', $this->email)->exists(),

            function () {
                $this->error('User already exists.');
            },

            function () {
                UserModel::factory()->createOne([
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                ]);

                $this->info('User created successfully.');
            });
    }
}
