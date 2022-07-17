<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Data;

use Tests\TestCase;

class UserCommandTest extends TestCase
{
    private string $table = 'users';

    public function testCommandExecuteWithSuccess(): void
    {
        $this->artisan('data:user')
            ->assertSuccessful();
    }

    public function testCommandCreatesNewUserInDatabase(): void
    {
        $this->artisan('data:user');

        $this->assertDatabaseHas($this->table, [
            'first_name' => 'Marcin',
            'last_name' => 'Witas',
            'email' => 'marcin.witas72@gmail.com',
        ])
        ->assertDatabaseCount($this->table, 1);
    }

    public function testCommandPrintProperlyMessageWhenSuccess(): void
    {
        $this->artisan('data:user')
            ->expectsOutput('User created successfully');
    }

    public function testCommandCannotCreateSameUserSecondTime(): void
    {
        $this->artisan('data:user');
        $this->artisan('data:user');

        $this->assertDatabaseHas($this->table, [
            'first_name' => 'Marcin',
            'last_name' => 'Witas',
            'email' => 'marcin.witas72@gmail.com',
        ])
        ->assertDatabaseCount($this->table, 1);
    }

    public function testCommandPrintProperlyMessageWhenUserAlreadyExists(): void
    {
        $this->artisan('data:user');
        $this->artisan('data:user')
            ->expectsOutput('User already exists');
    }
}
