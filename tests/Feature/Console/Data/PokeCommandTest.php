<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Data;

use App\Models\User;
use RuntimeException;
use Tests\TestCase;

class PokeCommandTest extends TestCase
{
    private User $user;

    private string $table = 'pokes';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    public function testExecuteWithSuccess(): void
    {
        $this->artisan("data:poke {$this->user->id} 1")
            ->assertSuccessful();
    }

    public function testCreatesProperlyAmountOfPokes(): void
    {
        $this->artisan("data:poke {$this->user->id} 4");

        $this->assertDatabaseCount($this->table, 4)
            ->assertDatabaseMissing($this->table, [
                'latest_initiator_id' => $this->user->id,
            ]);
    }

    public function testPrintProperlyMessageWhenSuccess(): void
    {
        $this->artisan("data:poke {$this->user->id} 1")
            ->expectsOutput('Poke(s) created successfully.');
    }

    public function testExceptionWhenUserNotPassed(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan('data:poke');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testExceptionWhenPassedUserNotExists(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan('data:poke 999');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testWhenAmountNotPassedCreateOnlyOnePoke(): void
    {
        $this->artisan("data:poke {$this->user->id}");

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotPassAmountLessThanOne(): void
    {
        $this->artisan("data:poke {$this->user->id} 0")
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Poke(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotPassStringAsAmountArgument(): void
    {
        $this->artisan("data:poke {$this->user->id} ugly_string")
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Poke(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCreateAsInitiatorWhenInitiatorOptionPassed(): void
    {
        $this->artisan("data:poke {$this->user->id} 2 --initiator")
            ->expectsOutput('Poke(s) created successfully.');

        $this->assertDatabaseCount($this->table, 2)
            ->assertDatabaseHas($this->table, [
                'latest_initiator_id' => $this->user->id,
            ]);
    }

    public function testOptionCanBeAliased(): void
    {
        $this->artisan("data:poke {$this->user->id} -I")
            ->expectsOutput('Poke(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'latest_initiator_id' => $this->user->id,
            ]);
    }
}
