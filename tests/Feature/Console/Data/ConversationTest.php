<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Data;

use App\Models\User;
use RuntimeException;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    private User $user;
    private User $friend;

    private string $table = 'messages';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friend = User::factory()->createOne();
    }

    public function testExecuteWithSuccess(): void
    {
        $this->artisan("data:conversation {$this->user->id} {$this->friend->id} 1")
            ->assertSuccessful();
    }

    public function testCreatesProperlyAmountOfMessages(): void
    {
        $this->artisan("data:conversation {$this->user->id} {$this->friend->id} 8");

        $this->assertDatabaseCount($this->table, 8);
    }

    public function testPrintProperlyMessageWhenSuccess(): void
    {
        $this->artisan("data:conversation {$this->user->id} {$this->friend->id} 1")
            ->expectsOutput('Conversation created successfully.');
    }

    public function testCreatesMessagesWhichWasSentByEitherUsers(): void
    {
        $this->artisan("data:conversation {$this->user->id} {$this->friend->id} 30");

        $this->assertDatabaseHas($this->table, [
            'sender_id' => $this->friend->id,
            'receiver_id' => $this->user->id,
        ])->assertDatabaseHas($this->table, [
            'sender_id' => $this->user->id,
            'receiver_id' => $this->friend->id,
        ]);
    }

    public function testExceptionWhenUserNotPassed(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan('data:conversation');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testExceptionWhenFriendNotPassed(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan("data:conversation {$this->user->id}");

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testExceptionWhenAmountNotPassed(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan("data:conversation {$this->user->id} {$this->friend->id}");

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotPassSameModelTwice(): void
    {
        $this->artisan("data:conversation {$this->user->id} {$this->user->id} 8")
            ->expectsOutput('Cannot pass same user twice.')
            ->doesntExpectOutput('Conversation created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotPassAmountLessThanOne(): void
    {
        $this->artisan("data:conversation {$this->user->id} {$this->friend->id} 0")
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Conversation created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotPassStringAsAmountArgument(): void
    {
        $this->artisan("data:conversation {$this->user->id} {$this->friend->id} ugly_string")
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Conversation created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }
}
