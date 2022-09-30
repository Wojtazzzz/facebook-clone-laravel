<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Data;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\ClearCommandTestCase as TestCase;

class ClearCommandTest extends TestCase
{
    use DatabaseMigrations;

    private string $command = 'data:clear';

    private array $tables = [
        'comments',
        'friendships',
        'hidden_posts',
        'likes',
        'messages',
        'notifications',
        'pokes',
        'posts',
        'saved_posts',
        'users',
    ];

    public function testCommandExecuteWithSuccess(): void
    {
        $this->artisan($this->command)
            ->assertSuccessful();
    }

    public function testCommandClearAllTabels(): void
    {
        $this->artisan($this->command)
            ->assertSuccessful();

        foreach ($this->tables as $table) {
            $this->assertDatabaseCount($table, 0);
        }
    }

    public function testCommandPrintProperlyMessageWhenSuccess(): void
    {
        $this->artisan($this->command)
            ->assertSuccessful()
            ->expectsOutput('Database cleared successfully.');
    }
}
