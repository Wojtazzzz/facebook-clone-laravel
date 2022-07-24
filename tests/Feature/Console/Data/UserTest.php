<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Data;

use Tests\TestCase;

class UserTest extends TestCase
{
    private string $command = 'data:user';
    private string $table = 'users';
    private string $friendshipTable = 'friendships';

    public function testExecuteWithSuccess(): void
    {
        $this->artisan($this->command)
            ->assertSuccessful();
    }

    public function testCreatesNewUserInDatabase(): void
    {
        $this->artisan($this->command);

        $this->assertDatabaseHas($this->table, [
            'first_name' => 'Marcin',
            'last_name' => 'Witas',
            'email' => 'marcin.witas72@gmail.com',
        ])
        ->assertDatabaseCount($this->table, 1);
    }

    public function testPrintProperlyMessageWhenSuccess(): void
    {
        $this->artisan($this->command)
            ->expectsOutput('User created successfully.');
    }

    public function testCannotCreateSameUserSecondTime(): void
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

    public function testPrintProperlyMessageWhenUserAlreadyExists(): void
    {
        $this->artisan($this->command);
        $this->artisan($this->command)
            ->expectsOutput('User already exists.')
            ->doesntExpectOutput('User created successfully.');
    }

    public function testDontCreateFriendshipWhenOptionNotPassed(): void
    {
        $this->artisan($this->command)
            ->expectsOutput('User created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseCount($this->friendshipTable, 0);
    }

    public function testCreateFriendshipWhenOptionPassed(): void
    {
        $this->artisan($this->command.' --friend')
            ->expectsOutput('User created successfully.')
            ->expectsOutput('Friendship created successfully.');

        $this->assertDatabaseCount($this->table, 2)
            ->assertDatabaseCount($this->friendshipTable, 1);
    }

    public function testOptionCanBeAliased(): void
    {
        $this->artisan($this->command.' -F')
            ->expectsOutput('User created successfully.')
            ->expectsOutput('Friendship created successfully.');

        $this->assertDatabaseCount($this->table, 2)
            ->assertDatabaseCount($this->friendshipTable, 1);
    }
}
