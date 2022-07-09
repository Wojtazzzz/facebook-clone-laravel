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

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $isExists = UserModel::where('email', $this->email)->exists();

        if ($isExists) {
            $this->info('');
            $this->error('User already exists');

            return 0;
        }

        UserModel::factory()->createOne([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
        ]);

        $this->info('');
        $this->info('User created successfully');

        return 0;
    }
}
