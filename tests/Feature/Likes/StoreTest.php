<?php

declare(strict_types=1);

namespace Tests\Feature\Likes;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PostLiked;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    private string $table = 'likes';

    public function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->user = User::factory()->createOne();

    }

    private function getRoute(Post | int $post): string
    {
        return route('api.posts.likes.store', [
            'post' => $post,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $response = $this->postJson($this->getRoute($post));
        $response->assertUnauthorized();
    }

    public function testCannotCreateLikeForPostWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->getRoute(99999));

        $response->assertNotFound();

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotCreateLikeForPostWhichIsAlreadyLiked(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        Like::factory()->createOne([
            'user_id' => $this->user->id,
            'likeable_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->getRoute($post));
        $response->assertJsonValidationErrorFor('post');

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCanCreateLike(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->getRoute($post));
        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCanLikePostWhichIsLikedByAnotherUser(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        Like::factory(2)->create([
            'likeable_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->getRoute($post));
        $response->assertCreated();
    }

    public function testLikeActionSendNotificationToPostsAuthor(): void
    {
        $author = User::factory()->createOne();

        $post = Post::factory()->createOne([
            'author_id' => $author->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->getRoute($post));
        $response->assertCreated();

        Notification::assertSentTo($author, PostLiked::class);
        Notification::assertTimesSent(1, PostLiked::class);

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testLikeActionNotSendNotificationWhenLikeOwnPost(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->getRoute($post));
        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1);

        Notification::assertNotSentTo($this->user, PostLiked::class);
        Notification::assertTimesSent(0, PostLiked::class);
    }
}
