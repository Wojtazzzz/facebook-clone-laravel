<?php

declare(strict_types=1);

namespace Tests\Feature\Posts;

use App\Models\User;
use Illuminate\Http\Testing\File;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    private string $postsStoreRoute;

    private string $postsTable = 'posts';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->postsStoreRoute = route('api.posts.store');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->postJson($this->postsStoreRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->postsStoreRoute, [
            'content' => 'Simple post',
        ]);

        $response->assertCreated();
    }

    public function testCannotCreatePostWithoutData(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->postsStoreRoute);

        $this->assertDatabaseCount($this->postsTable, 0);
        $response->assertUnprocessable();
    }

    public function testCanCreatePostWithOnlyContent(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->postsStoreRoute, [
            'content' => 'Simple post',
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->postsTable, 1)
            ->assertDatabaseHas($this->postsTable, [
                'content' => 'Simple post',
                'author_id' => $this->user->id,
                'images' => '[]',
            ]);
    }

    public function testPostContentMustBeAtLeastTwoCharactersLong(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->postsStoreRoute, [
            'content' => 'S',
        ]);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testPostContentCannotBeToLong(): void
    {
        $content = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

        $response = $this->actingAs($this->user)->postJson($this->postsStoreRoute, [
            'content' => $content,
        ]);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testCanCreatePostWithOnlyImages(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->postsStoreRoute, [
            'images' => [
                new File('test.jpg', tmpfile()),
            ],
        ]);

        $response->assertCreated()
            ->assertJsonCount(1, 'data.images');

        $this->assertDatabaseCount($this->postsTable, 1)
            ->assertDatabaseHas($this->postsTable, [
                'content' => null,
                'author_id' => $this->user->id,
            ]);
    }

    public function testCanCreatePostWithManyImages(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->postsStoreRoute, [
            'images' => [
                new File('test.jpg', tmpfile()),
                new File('test.jpg', tmpfile()),
                new File('test.jpg', tmpfile()),
                new File('test.jpg', tmpfile()),
                new File('test.jpg', tmpfile()),
                new File('test.jpg', tmpfile()),
            ],
        ]);

        $response->assertCreated()
            ->assertJsonCount(6, 'data.images');
    }

    public function testCanPassOnlyFilesWithSpecifiedTypes(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->postsStoreRoute, [
            'images' => [
                new File('test.jpg', tmpfile()),
                new File('test.jpeg', tmpfile()),
                new File('test.png', tmpfile()),
                new File('test.bmp', tmpfile()),
                new File('test.gif', tmpfile()),
                new File('test.svg', tmpfile()),
                new File('test.webp', tmpfile()),
            ],
        ]);

        $response->assertCreated()
            ->assertJsonCount(7, 'data.images');

        $response = $this->actingAs($this->user)->postJson($this->postsStoreRoute, [
            'images' => [
                new File('test.exe', tmpfile()),
                new File('test.txt', tmpfile()),
                new File('test.pdf', tmpfile()),
                new File('test.bin', tmpfile()),
            ],
        ]);

        $response->assertUnprocessable();
    }

    public function testCannotPassEmptyArrayAsImages(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->postsStoreRoute, [
            'images' => [],
        ]);

        $response->assertUnprocessable();
    }

    public function testModelAutoFillAuthorIdWithUserId(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->postsStoreRoute, [
            'content' => 'Simple post',
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->postsTable, 1)
            ->assertDatabaseHas($this->postsTable, [
                'content' => 'Simple post',
                'author_id' => $this->user->id,
            ]);
    }
}
