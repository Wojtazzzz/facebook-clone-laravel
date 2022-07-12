<?php

declare(strict_types=1);

namespace Tests\Feature\Comments\Posts;

use App\Models\Post;
use App\Models\User;
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
        $this->route = route('api.comments.posts.store', $this->post->id);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->postJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple comment',
                'resource_id' => $this->post->id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCanCreateComment(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple comment',
                'resource_id' => $this->post->id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'content' => 'Simple comment',
                'resource_id' => $this->post->id,
                'author_id' => $this->user->id,
            ]);
    }

    public function testCannotCreateCommentWithoutContent(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'resource_id' => $this->post->id,
            ]);

        $response->assertJsonValidationErrorFor('content');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotCreateCommentWithOneLetterLengthContent(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'S',
                'resource_id' => $this->post->id,
            ]);

        $response->assertJsonValidationErrorFor('content');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testPassedEmptyStringValuesAreTreatingAsNullValues(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => '',
                'resource_id' => '',
            ]);

        $response->assertJsonValidationErrorFor('content')
            ->assertJsonValidationErrorFor('resource_id');
    }

    public function testCannotCreateCommentWithToLongContent(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                'resource_id' => $this->post->id,
            ]);

        $response->assertJsonValidationErrorFor('content');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotCreateCommentWithoutResourceId(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple comment',
            ]);

        $response->assertJsonValidationErrorFor('resource_id');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotCreateCommentForPostWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->route, [
                'content' => 'Simple comment',
                'resource_id' => 99999,
            ]);

        $response->assertJsonValidationErrorFor('resource_id');

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testResponseIncludesNewComment(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->route, [
            'content' => 'Simple comment',
            'resource_id' => $this->post->id,
        ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'content' => 'Simple comment',
                'resource_id' => $this->post->id,
            ]);
    }
}
