<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Data;

use Tests\TestCase;

class UsersCommandTest extends TestCase
{
    private string $command = 'data:users';

    private string $table = 'users';

    public function testExecuteWithSuccess(): void
    {
        $this->artisan($this->command)
            ->assertSuccessful();
    }

    public function testCreateSpecificAmountOfUsers(): void
    {
        $this->artisan($this->command.' 7');

        $this->assertDatabaseCount($this->table, 7);
    }

    public function testPrintProperlyMessageWhenSuccess(): void
    {
        $this->artisan($this->command)
            ->expectsOutput('User(s) created successfully.');
    }

    public function testCannotPassStringAsAmountArgument(): void
    {
        $this->artisan($this->command.' ugly_string')
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('User(s) created successfully.');
    }

    public function testCreateOnlyOneUserWhenAmountNotPassed(): void
    {
        $this->artisan($this->command)
            ->expectsOutput('User(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1);
    }
}
