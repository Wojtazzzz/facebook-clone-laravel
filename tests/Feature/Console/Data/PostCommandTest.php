<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Data;

use App\Models\User;
use Tests\TestCase;

class PostCommandTest extends TestCase
{
    private string $table = 'posts';
    private string $commentsTable = 'comments';

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testExecuteWithSuccess(): void
    {
        $this->artisan('data:post 1')
            ->assertSuccessful();
    }

    public function testCreatesProperlyAmountOfPosts(): void
    {
        $this->artisan('data:post 6');

        $this->assertDatabaseCount($this->table, 6);
    }

    public function testPrintProperlyMessageWhenSuccess(): void
    {
        $this->artisan('data:post 1')
            ->expectsOutput('Post(s) created successfully.');
    }

    public function testWhenAmountNotPassedCreateOnlyOnePost(): void
    {
        $this->artisan('data:post');

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotPassAmountLessThanOne(): void
    {
        $this->artisan('data:post 0')
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Post(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotPassStringAsAmountArgument(): void
    {
        $this->artisan('data:post ugly_string')
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Post(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCreateWithSpecificAuthorWhenAuthorOptionPassed(): void
    {
        $user = User::factory()->createOne([
            'id' => 999,
        ]);

        $this->artisan("data:post --author={$user->id}")
            ->expectsOutput('Post(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'author_id' => $user->id,
            ]);
    }

    public function testAuthorOptionCanBeAliased(): void
    {
        $user = User::factory()->createOne([
            'id' => 999,
        ]);

        $this->artisan("data:post -A {$user->id}")
            ->expectsOutput('Post(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'author_id' => $user->id,
            ]);
    }

    public function testCannotPassNotExistingUserAsAuthorOption(): void
    {
        $this->expectErrorMessage('No query results for model [App\Models\User] 99999');

        $this->artisan('data:post -A 99999')
            ->doesntExpectOutput('Post(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testDontCreatesPostWithCommentsWhenCommentsOptionNotPassed(): void
    {
        $this->artisan('data:post')
            ->expectsOutput('Post(s) created successfully.')
            ->doesntExpectOutput('Comment(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseCount($this->commentsTable, 0);
    }

    public function testCreatesProperlyAmountOfComments(): void
    {
        $this->artisan('data:post --comments=2')
            ->expectsOutput('Post(s) created successfully.')
            ->expectsOutput('Comment(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseCount($this->commentsTable, 2)
            ->assertDatabaseHas($this->commentsTable, [
                'resource_id' => 1,
            ])
            ->assertDatabaseMissing($this->commentsTable, [
                'resource_id' => 2,
            ]);
    }

    public function testCommentsOptionCanBeAliased(): void
    {
        $this->artisan('data:post -C 9')
            ->expectsOutput('Post(s) created successfully.')
            ->expectsOutput('Comment(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseCount($this->commentsTable, 9);
    }
}
