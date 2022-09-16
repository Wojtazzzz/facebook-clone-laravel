<?php

declare(strict_types=1);

namespace Tests\Feature\Comments\Posts;

use App\Models\Post;
use App\Models\User;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    private Post $post;

    private string $route;

    private string $table = 'comments';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->post = Post::factory()->createOne();
        $this->route = route('api.posts.comments.store', $this->post);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->postJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanCreateComment(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple comment',
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'content' => 'Simple comment',
            ]);
    }

    public function testCannotCreateCommentWithoutContent(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route);

        $response->assertJsonValidationErrorFor('content');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testPassedEmptyStringValuesAreTreatingAsNullValues(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => '',
            ]);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testCannotCreateCommentWithToLongContent(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            ]);

        $response->assertJsonValidationErrorFor('content');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotCreateCommentWithoutResourceId(): void
    {
        $this->expectException(UrlGenerationException::class);

        $route = route('api.posts.comments.store');

        $this->actingAs($this->user)
            ->postJson($route, [
                'content' => 'Simple comment',
            ]);

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotCreateCommentForPostWhichNotExists(): void
    {
        $route = route('api.posts.comments.store', 99999);

        $response = $this->actingAs($this->user)
            ->postJson($route, [
                'content' => 'Simple comment',
            ]);

        $response->assertNotFound();

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testResponseIncludesNewComment(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->route, [
            'content' => 'Simple comment',
        ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'content' => 'Simple comment',
                'commentable_id' => $this->post->id,
            ]);
    }
}
