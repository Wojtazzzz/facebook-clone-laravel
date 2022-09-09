<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Data;

use App\Enums\FriendshipStatus;
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
        $this->artisan("data:friendship {$this->user->id} 4")
            ->assertSuccessful();

        $this->assertDatabaseCount($this->table, 4);
    }

    public function testPrintProperlyMessageWhenSuccess(): void
    {
        $this->artisan("data:friendship {$this->user->id} 1")
            ->assertSuccessful()
            ->expectsOutput('Friendship(s) created successfully.');
    }

    public function testUserMustBePassed(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan('data:friendship')
            ->assertFailed();

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testExceptionWhenPassedUserNotExists(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan('data:friendship 999')
            ->assertFailed();

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testWhenAmountNotPassedCreateOnlyOneFriendship(): void
    {
        $this->artisan("data:friendship {$this->user->id}")
            ->assertSuccessful();

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotPassAmountLessThanOne(): void
    {
        $this->artisan("data:friendship {$this->user->id} 0")
            ->assertFailed()
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Friendship(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotPassStringAsAmountArgument(): void
    {
        $this->artisan("data:friendship {$this->user->id} ugly_string")
            ->assertFailed()
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Friendship(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCreateWithConfirmedStatusWhenNoOptionPassed(): void
    {
        $this->artisan("data:friendship {$this->user->id}")
            ->assertSuccessful()
            ->expectsOutput('Friendship(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'status' => FriendshipStatus::CONFIRMED,
            ]);
    }

    public function testCreateWithConfirmedStatusWhenPassedConfirmedOption(): void
    {
        $this->artisan("data:friendship {$this->user->id} --status=confirmed")
            ->assertSuccessful()
            ->expectsOutput('Friendship(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'user_id' => $this->user->id,
                'status' => FriendshipStatus::CONFIRMED,
            ]);
    }

    public function testCreateWithConfirmedStatusWhenPassedPendingOption(): void
    {
        $this->artisan("data:friendship {$this->user->id} --status=pending")
            ->assertSuccessful()
            ->expectsOutput('Friendship(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'friend_id' => $this->user->id,
                'status' => FriendshipStatus::PENDING,
            ]);
    }

    public function testCreateWithConfirmedStatusWhenPassedBlockedOption(): void
    {
        $this->artisan("data:friendship {$this->user->id} --status=blocked")
            ->assertSuccessful()
            ->expectsOutput('Friendship(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'user_id' => $this->user->id,
                'status' => FriendshipStatus::BLOCKED,
            ]);
    }

    public function testOptionCanBeAliased(): void
    {
        $this->artisan("data:friendship {$this->user->id} -S pending")
            ->assertSuccessful()
            ->expectsOutput('Friendship(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'status' => FriendshipStatus::PENDING,
            ]);
    }
}
