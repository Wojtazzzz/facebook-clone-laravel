<?php

namespace Tests\Feature\Comments\Posts;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;
    private Post $post;

    private string $commentsStoreRoute;

    private string $commentsTable = 'comments';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->post = Post::factory()->createOne();
        $this->commentsStoreRoute = route('api.comments.posts.store', $this->post->id);
    }

    public function testCannotUseAsUnauthorized()
    {
        $response = $this->postJson($this->commentsStoreRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized()
    {
        $response = $this->actingAs($this->user)->postJson($this->commentsStoreRoute, [
            'content' => 'Simple comment',
            'resource_id' => $this->post->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->commentsTable, 1);
    }

    public function testCanCreateComment()
    {
        $response = $this->actingAs($this->user)->postJson($this->commentsStoreRoute, [
            'content' => 'Simple comment',
            'resource_id' => $this->post->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->commentsTable, 1)
            ->assertDatabaseHas($this->commentsTable, [
                'content' => 'Simple comment',
                'resource_id' => $this->post->id,
                'author_id' => $this->user->id,
            ]);
    }

    public function testCannotCreateCommentWithoutContent()
    {
        $response = $this->actingAs($this->user)->postJson($this->commentsStoreRoute, [
            'resource_id' => $this->post->id,
        ]);

        $response->assertJsonValidationErrorFor('content');
        $this->assertDatabaseCount($this->commentsTable, 0);
    }

    public function testCannotCreateCommentWithOneLetterLengthContent()
    {
        $response = $this->actingAs($this->user)->postJson($this->commentsStoreRoute, [
            'content' => 'S',
            'resource_id' => $this->post->id,
        ]);

        $response->assertJsonValidationErrorFor('content');
        $this->assertDatabaseCount($this->commentsTable, 0);
    }

    public function testCannotCreateCommentWithToLongContent()
    {
        $response = $this->actingAs($this->user)->postJson($this->commentsStoreRoute, [
            'content' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            'resource_id' => $this->post->id,
        ]);

        $response->assertJsonValidationErrorFor('content');
        $this->assertDatabaseCount($this->commentsTable, 0);
    }

    public function testCannotCreateCommentWithoutResourceId()
    {
        $response = $this->actingAs($this->user)->postJson($this->commentsStoreRoute, [
            'content' => 'Simple comment',
        ]);

        $response->assertJsonValidationErrorFor('resource_id');
        $this->assertDatabaseCount($this->commentsTable, 0);
    }

    public function testCannotCreateCommentForPostWhichNotExists()
    {
        $response = $this->actingAs($this->user)->postJson($this->commentsStoreRoute, [
            'content' => 'Simple comment',
            'resource_id' => 99999,
        ]);

        $response->assertJsonValidationErrorFor('resource_id');
        $this->assertDatabaseCount($this->commentsTable, 0);
    }

    public function testResponseIncludesNewComment()
    {
        $response = $this->actingAs($this->user)->postJson($this->commentsStoreRoute, [
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
