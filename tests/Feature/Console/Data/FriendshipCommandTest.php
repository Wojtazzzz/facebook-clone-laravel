<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Data;

use App\Models\User;
use RuntimeException;
use Tests\TestCase;

class FriendshipCommandTest extends TestCase
{
    private User $user;

    private string $table = 'friendships';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    public function testExecuteWithSuccess(): void
    {
        $this->artisan("data:friendship {$this->user->id} 1")
            ->assertSuccessful();
    }

    public function testCreatesProperlyAmountOfFriendships(): void
    {
        $this->artisan("data:friendship {$this->user->id} 4");

        $this->assertDatabaseCount($this->table, 4);
    }

    public function testPrintProperlyMessageWhenSuccess(): void
    {
        $this->artisan("data:friendship {$this->user->id} 1")
            ->expectsOutput('Friendship(s) created successfully.');
    }

    public function testExceptionWhenUserNotPassed(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan('data:friendship');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testExceptionWhenPassedUserNotExists(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan('data:friendship 999');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testWhenAmountNotPassedCreateOnlyOneFriendship(): void
    {
        $this->artisan("data:friendship {$this->user->id}");

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotPassAmountLessThanOne(): void
    {
        $this->artisan("data:friendship {$this->user->id} 0")
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Friendship(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotPassStringAsAmountArgument(): void
    {
        $this->artisan("data:friendship {$this->user->id} ugly_string")
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Friendship created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }
}
