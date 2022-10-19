<?php

declare(strict_types=1);

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private User $user;

    private string $table = 'posts';

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->createOne();
    }

    private function getRoute(Post | int $post): string
    {
        return route('api.posts.update', [
            'post' => $post,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $post = Post::factory()->createOne();

        $response = $this->postJson($this->getRoute($post));
        $response->assertUnauthorized();
    }

    public function testCannotUpdateSomebodysPost(): void
    {
        $post = Post::factory()->createOne();

        $response = $this->actingAs($this->user)->postJson($this->getRoute($post));
        $response->assertForbidden();
    }

    public function testCanUpdatePostsContent(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'images' => [
                $this->faker->picsumStaticRandomUrl(850, 350),
            ],
            'content' => 'Test content',
        ]);

        $images = Post::findOrFail($post->id)->value('images');

        $this->assertCount(1, $images);
        $this->assertDatabaseHas($this->table, [
            'id' => $post->id,
            'content' => 'Test content',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->getRoute($post), [
                'content' => 'Simple content',
            ]);

        $response->assertNoContent();

        $this->assertDatabaseHas($this->table, [
            'id' => $post->id,
            'content' => 'Simple content',
        ]);

        $images = Post::findOrFail($post->id)->value('images');

        $this->assertCount(1, $images);
    }

    public function testCanRemoveContentFromPost(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'images' => [
                $this->faker->picsumStaticRandomUrl(850, 350),
            ],
            'content' => 'Test content',
        ]);

        $images = Post::findOrFail($post->id)->value('images');

        $this->assertCount(1, $images);
        $this->assertDatabaseHas($this->table, [
            'id' => $post->id,
            'content' => 'Test content',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->getRoute($post), [
                'content' => '',
            ]);

        $response->assertNoContent();

        $this->assertDatabaseHas($this->table, [
            'id' => $post->id,
            'content' => null,
        ]);

        $images = Post::findOrFail($post->id)->value('images');

        $this->assertCount(1, $images);
    }

    public function testCannotUpdateContentToTooShortContent(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'images' => [
                $this->faker->picsumStaticRandomUrl(850, 350),
            ],
            'content' => 'Test content',
        ]);

        $this->assertDatabaseHas($this->table, [
            'id' => $post->id,
            'content' => 'Test content',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->getRoute($post), [
                'content' => 'T',
            ]);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testCannotUpdateContentToTooLongContent(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'images' => [
                $this->faker->picsumStaticRandomUrl(850, 350),
            ],
            'content' => 'Test content',
        ]);

        $this->assertDatabaseHas($this->table, [
            'id' => $post->id,
            'content' => 'Test content',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->getRoute($post), [
                'content' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            ]);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testCanAddContentToPostWhichHaveNotContent(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'images' => [
                $this->faker->picsumStaticRandomUrl(850, 350),
            ],
            'content' => null,
        ]);

        $this->assertDatabaseHas($this->table, [
            'id' => $post->id,
            'content' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->getRoute($post), [
                'content' => 'Test content',
            ]);

        $response->assertNoContent();

        $this->assertDatabaseHas($this->table, [
            'id' => $post->id,
            'content' => 'Test content',
        ]);
    }

    public function testCanAddImagesToPostWhichHaveImages(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'content' => 'Simple content',
            'images' => [
                $this->faker->picsumStaticRandomUrl(850, 350),
            ],
        ]);

        $images = Post::where('id', $post->id)->value('images');

        $this->assertCount(1, $images);

        $response = $this->actingAs($this->user)
        ->postJson($this->getRoute($post), [
            'images' => [
                new File('test.jpg', tmpfile()),
            ],
        ]);

        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseHas($this->table, [
            'author_id' => $this->user->id,
            'content' => 'Simple content',
        ]);

        $images = Post::where('id', $post->id)->value('images');

        $this->assertCount(2, $images);
    }

    public function testCanAddImagesToPostWhichHaveNoImages(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'content' => 'Simple content',
            'images' => [],
        ]);

        $images = Post::where('id', $post->id)->value('images');

        $this->assertCount(0, $images);

        $response = $this->actingAs($this->user)
            ->postJson($this->getRoute($post), [
                'images' => [
                    new File('test.jpg', tmpfile()),
                ],
            ]);

        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseHas($this->table, [
            'author_id' => $this->user->id,
            'content' => 'Simple content',
        ]);

        $images = Post::where('id', $post->id)->value('images');

        $this->assertCount(1, $images);
    }

    public function testCanRemoveImagesFromPost(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'content' => 'Test content',
            'images' => [
                $this->faker->picsumStaticRandomUrl(850, 350),
            ],
        ]);

        $images = Post::where('id', $post->id)->value('images');

        $this->assertCount(1, $images);

        $response = $this->actingAs($this->user)
            ->postJson($this->getRoute($post), [
                'imagesToDelete' => [
                    $images[0],
                ],
            ]);

        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabaseHas($this->table, [
            'content' => 'Test content',
        ]);

        $images = Post::where('id', $post->id)->value('images');

        $this->assertCount(0, $images);
    }

    public function testCanRemoveImagesFromPostAndAddAnotherOneInSameResponse(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'images' => [
                $this->faker->picsumStaticRandomUrl(850, 350),
                $this->faker->picsumStaticRandomUrl(850, 350),
                $this->faker->picsumStaticRandomUrl(850, 350),
            ],
        ]);

        $images = Post::where('id', $post->id)->value('images');

        $this->assertCount(3, $images);

        $response = $this->actingAs($this->user)
            ->postJson($this->getRoute($post), [
                'images' => [
                    new File('test.jpg', tmpfile()),
                ],
                'imagesToDelete' => [
                    $images[0],
                    $images[1],
                ],
            ]);

        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 1);

        $images = Post::where('id', $post->id)->value('images');

        $this->assertCount(2, $images);
    }

    public function testResponseNotCrashWhenPassedImgToDeleteNotExists(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'content' => 'Test content',
            'images' => [
                $this->faker->picsumStaticRandomUrl(850, 350),
            ],
        ]);

        $images = Post::where('id', $post->id)->value('images');

        $this->assertCount(1, $images);

        $response = $this->actingAs($this->user)
            ->postJson($this->getRoute($post), [
                'imagesToDelete' => [
                    'not-exist.jpg',
                ],
            ]);

        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 1);
        $this->assertDatabasehas($this->table, [
            'content' => 'Test content',
        ]);

        $images = Post::where('id', $post->id)->value('images');

        $this->assertCount(1, $images);
    }

    public function testCannotAddToImagesFileWhichHasNotImageExtenstion(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'images' => [
                $this->faker->picsumStaticRandomUrl(850, 350),
            ],
        ]);

        $images = Post::where('id', $post->id)->value('images');

        $this->assertCount(1, $images);

        $response = $this->actingAs($this->user)
            ->postJson($this->getRoute($post), [
                'images' => [
                    new File('test.pdf', tmpfile()),
                ],
            ]);

        $response->assertJsonValidationErrorFor('images.0');
    }

    public function testDeletePostAfterPassNoContentAndAllImagesToDelete(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'content' => 'Test content',
            'images' => [
                $this->faker->picsumStaticRandomUrl(850, 350),
            ],
        ]);

        $images = Post::where('id', $post->id)->value('images');

        $response = $this->actingAs($this->user)
            ->postJson($this->getRoute($post), [
                'imagesToDelete' => [
                    $images[0],
                ],
                'content' => '',
            ]);

        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 0);
    }
}
