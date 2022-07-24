<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Data;

use Tests\TestCase;

class UserCommandTest extends TestCase
{
    private string $command = 'data:user';
    private string $table = 'users';

    public function testCommandExecuteWithSuccess(): void
    {
        $this->artisan($this->command)
            ->assertSuccessful();
    }

    public function testCommandCreatesNewUserInDatabase(): void
    {
        $this->artisan($this->command);

        $this->assertDatabaseHas($this->table, [
            'first_name' => 'Marcin',
            'last_name' => 'Witas',
            'email' => 'marcin.witas72@gmail.com',
        ])
        ->assertDatabaseCount($this->table, 1);
    }

    public function testCommandPrintProperlyMessageWhenSuccess(): void
    {
        $this->artisan($this->command)
            ->expectsOutput('User created successfully');
    }

    public function testCommandCannotCreateSameUserSecondTime(): void
    {
        $this->artisan($this->command);
        $this->artisan($this->command);

        $this->assertDatabaseHas($this->table, [
            'first_name' => 'Marcin',
            'last_name' => 'Witas',
            'email' => 'marcin.witas72@gmail.com',
        ])
        ->assertDatabaseCount($this->table, 1);
    }

    public function testCommandPrintProperlyMessageWhenUserAlreadyExists(): void
    {
        $this->artisan($this->command);
        $this->artisan($this->command)
            ->expectsOutput('User already exists');
    }
}
