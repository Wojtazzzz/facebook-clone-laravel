<?php

declare(strict_types=1);

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private User $user;
    private Post $post;

    private string $route;
    private string $table = 'posts';

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->createOne();
        $this->post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);
        $this->route = route('api.posts.destroy', $this->post);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Storage::fake('public');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->deleteJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->deleteJson($this->route);
        $response->assertNoContent();
    }

    public function testCanDeleteOwnPost(): void
    {
        $response = $this->actingAs($this->user)->deleteJson($this->route);

        $response->assertNoContent();
        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotDeletePostWhichNotExist(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('api.posts.destroy', 99999));

        $response->assertNotFound();
    }

    public function testCannotDeleteSomebodysPost(): void
    {
        $post = Post::factory()->createOne();

        $response = $this->actingAs($this->user)
            ->deleteJson(route('api.posts.destroy', $post));

        $response->assertForbidden();

        $this->assertDatabaseCount($this->table, 2)
            ->assertDatabaseHas($this->table, [
                'id' => $post->id,
                'content' => $post->content,
                'author_id' => $post->author_id,
            ]);
    }

    public function testDeleteAllPostImagesFromStorageDuringPostDeleting(): void
    {
        $files = [
            new File('test.gif', tmpfile()),
            new File('test.svg', tmpfile()),
        ];

        $paths = [];

        foreach ($files as $file) {
            $path = $file->store('posts', 'public');
            $paths[] = str_replace('public', '', $path);
        }

        $this->assertCount(2, Storage::disk('public')->allFiles('posts'));

        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'images' => $paths,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('api.posts.destroy', $post));

        $response->assertNoContent();

        $this->assertCount(0, Storage::disk('public')->allFiles('posts'));
    }

    public function testDeletingPostRemovesOnlyRelevantImages(): void
    {
        Storage::disk('public')
            ->put('posts', new File('newTestFile.png', tmpfile()));

        $files = [
            new File('test.gif', tmpfile()),
            new File('test.svg', tmpfile()),
        ];

        $paths = [];

        foreach ($files as $file) {
            $path = $file->store('posts', 'public');
            $paths[] = str_replace('public', '', $path);
        }

        $this->assertCount(3, Storage::disk('public')->allFiles('posts'));

        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'images' => $paths,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson(route('api.posts.destroy', $post));

        $response->assertNoContent();

        $this->assertCount(1, Storage::disk('public')->allFiles('posts'));
    }
}
