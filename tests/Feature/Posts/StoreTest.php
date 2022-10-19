<?php

declare(strict_types=1);

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    private string $route;

    private string $table = 'posts';

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->createOne();
        $this->route = route('api.posts.store');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->postJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCannotCreatePostWithoutData(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->route);
        $response->assertJsonValidationErrorFor('content')
            ->assertJsonValidationErrorFor('images');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCanCreatePostWithOnlyContent(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple post',
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'content' => 'Simple post',
                'author_id' => $this->user->id
            ]);

        $post = Post::firstWhere('content', 'Simple post');

        $this->assertEmpty($post->images);
    }

    public function testPostContentMustBeAtLeastTwoCharactersLong(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'S',
            ]);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testPostContentCannotBeToLong(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            ]);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testCanCreatePostWithOnlyImages(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'images' => [
                    new File('test.jpg', tmpfile()),
                ],
            ]);

        $response->assertCreated()
            ->assertJsonCount(1, 'images');

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'content' => null,
                'author_id' => $this->user->id,
            ]);
    }

    public function testCanCreatePostWithManyImages(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
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
            ->assertJsonCount(6, 'images');
    }

    public function testCanPassOnlyFilesWithSpecifiedTypes(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
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
            ->assertJsonCount(7, 'images');

        $response = $this->actingAs($this->user)->postJson($this->route, [
            'images' => [
                new File('test.exe', tmpfile()),
                new File('test.txt', tmpfile()),
                new File('test.pdf', tmpfile()),
                new File('test.bin', tmpfile()),
            ],
        ]);

        $response->assertJsonValidationErrorFor('images.0')
            ->assertJsonValidationErrorFor('images.1')
            ->assertJsonValidationErrorFor('images.2')
            ->assertJsonValidationErrorFor('images.3');
    }

    public function testCannotPassEmptyArrayAsImages(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'images' => [],
            ]);

        $response->assertJsonValidationErrorFor('images');
    }

    public function testModelAutoFillAuthorIdWithUserId(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple post',
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'content' => 'Simple post',
                'author_id' => $this->user->id,
            ]);
    }

    public function testCanCreatePostWithOnlyImagesWhenPassedImagesAndEmptyContent(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => '',
                'images' => [
                    new File('test.gif', tmpfile()),
                    new File('test.svg', tmpfile()),
                ],
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testPassedImagesAreStoreInStorage(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'images' => [
                    new File('test.gif', tmpfile()),
                    new File('test.jpg', tmpfile()),
                    new File('test.svg', tmpfile()),
                ],
            ]);

        $response->assertCreated();

        $this->assertCount(3, Storage::disk('public')->allFiles('posts'));
    }
}
