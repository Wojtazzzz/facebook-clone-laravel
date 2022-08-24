<?php

declare(strict_types=1);

namespace Tests\Feature\Likes;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    private string $route;

    private string $table = 'likes';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $this->route = route('api.posts.likes.store', [
            'post' => $this->post,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->postJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->route);
        $response->assertCreated();
    }

    public function testNotPassedPostIdReturnsNotFound(): void
    {
        $this->expectException(UrlGenerationException::class);

        $route = route('api.posts.likes.store');

        $this->actingAs($this->user)->postJson($route);
    }

    public function testCannotCreateLikeForPostWhichNotExists(): void
    {
        $route = route('api.posts.likes.store', [
            'post' => 99999,
        ]);

        $response = $this->actingAs($this->user)->postJson($route);

        $response->assertNotFound();
        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotCreateLikeForPostWhichIsAlreadyLiked(): void
    {
        Like::factory()->createOne([
            'user_id' => $this->user->id,
            'likeable_id' => $this->post->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->route);
        $response->assertJsonValidationErrorFor('post');

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCanCreateLike(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->route);
        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCanLikePostWhichIsLikedByAnotherUser(): void
    {
        Like::factory(2)->create([
            'likeable_id' => $this->post->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->route);
        $response->assertCreated();
    }
}
