<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Data;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use RuntimeException;
use Tests\TestCase;

class CommentCommandTest extends TestCase
{
    private Post $post;

    private string $table = 'comments';

    public function setUp(): void
    {
        parent::setUp();

        $this->post = Post::factory()->createOne();
    }

    public function testExecuteWithSuccess(): void
    {
        $this->artisan("data:comment {$this->post->id} 1")
            ->assertSuccessful();
    }

    public function testCannotPassNoPost(): void
    {
        $this->expectException(RuntimeException::class);

        $this->artisan('data:comment');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotPassPostWhichNotExists(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->artisan('data:comment 99999');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCreatesProperlyAmountOfComments(): void
    {
        $this->artisan("data:comment {$this->post->id} 4");

        $this->assertDatabaseCount($this->table, 4);
    }

    public function testPrintProperlyMessageWhenSuccess(): void
    {
        $this->artisan("data:comment {$this->post->id} 1")
            ->expectsOutput('Comment(s) created successfully.');
    }

    public function testWhenAmountNotPassedCreateOnlyOneComment(): void
    {
        $this->artisan("data:comment {$this->post->id}");

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotPassAmountLessThanOne(): void
    {
        $this->artisan("data:comment {$this->post->id} 0")
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Comment(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotPassStringAsAmountArgument(): void
    {
        $this->artisan("data:comment {$this->post->id} ugly_string")
            ->expectsOutput('Amount must be integer greater than 0.')
            ->doesntExpectOutput('Comment(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCreateWithSpecificAuthorWhenAuthorOptionPassed(): void
    {
        $user = User::factory()->createOne([
            'id' => 999,
        ]);

        $this->artisan("data:comment {$this->post->id} --author={$user->id}")
            ->expectsOutput('Comment(s) created successfully.');

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

        $this->artisan("data:comment {$this->post->id} -A {$user->id}")
            ->expectsOutput('Comment(s) created successfully.');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'author_id' => $user->id,
            ]);
    }

    public function testCannotPassNotExistingUserAsAuthorOption(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->artisan("data:comment {$this->post->id} -A 99999")
            ->doesntExpectOutput('Comment(s) created successfully.');

        $this->assertDatabaseCount($this->table, 0);
    }
}
