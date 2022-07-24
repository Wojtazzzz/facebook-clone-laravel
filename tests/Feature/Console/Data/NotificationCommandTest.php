<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Data;

use App\Models\User;
use RuntimeException;
use Tests\TestCase;

class NotificationCommandTest extends TestCase
{
    private User $user;

    private string $table = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    public function testExecuteWithSuccess(): void
    {
        $this->artisan("data:notification {$this->user->id} 1")
            ->assertSuccessful();
    }

    public function testCreatesProperlyAmountOfNotifications(): void
    {
        $this->artisan("data:notification {$this->user->id} 4");

        $this->assertDatabaseCount($this->table, 4)
            ->assertDatabaseHas($this->table, [
                'notifiable_id' => $this->user->id,
            ]);
    }

    public function testPrintProperlyMessageWhenSuccess(): void
    {
        $this->artisan("data:notification {$this->user->id} 1")
            ->expectsOutput('Notification(s) created successfully.');
    }

    public function testExceptionWhenUserNotPassed(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan('data:notification');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testExceptionWhenPassedUserNotExists(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan('data:notification 999');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testWhenAmountNotPassedCreateOnlyOneNotification(): void
    {
        $this->artisan("data:notification {$this->user->id}");

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotPassAmountLessThanOne(): void
    {
        $this->artisan("data:notification {$this->user->id} 0")
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Notification(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotPassStringAsAmountArgument(): void
    {
        $this->artisan("data:notification {$this->user->id} ugly_string")
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Notification(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }
}
