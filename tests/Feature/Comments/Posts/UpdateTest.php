<?php

declare(strict_types=1);

namespace Tests\Feature\Comments\Posts;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private User $user;
    private Post $post;
    private Comment $comment;

    private string $commentsUpdateRoute;

    private string $commentsTable = 'comments';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->post = Post::factory()->createOne();
        $this->comment = Comment::factory()->createOne([
            'content' => 'Simple content',
            'author_id' => $this->user->id,
            'resource_id' => $this->post->id,
        ]);

        $this->commentsUpdateRoute = route('api.comments.posts.update', [
            'resourceId' => $this->post->id,
            'comment' => $this->comment,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->putJson($this->commentsUpdateRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->commentsUpdateRoute, [
                'content' => 'Simple comment',
            ]);

        $response->assertOk();

        $this->assertDatabaseCount($this->commentsTable, 1);
    }

    public function testCanUpdateOwnComment(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->commentsUpdateRoute, [
                'content' => 'Simple updated comment',
            ]);

        $response->assertOk();

        $this->assertDatabaseCount($this->commentsTable, 1)
            ->assertDatabaseHas($this->commentsTable, [
                'content' => 'Simple updated comment',
                'author_id' => $this->user->id,
            ]);
    }

    public function testCannotUpdateSomebodysComment(): void
    {
        $friend = User::factory()->createOne();
        $comment = Comment::factory()->createOne([
            'author_id' => $friend->id,
        ]);

        $route = route('api.comments.posts.update', [
            'resourceId' => $this->post->id,
            'comment' => $comment,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson($route, [
                'content' => 'Simple updated comment',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing($this->commentsTable, [
                'content' => 'Simple updated comment',
                'author_id' => $this->user->id,
            ]);
    }

    public function testCannotUpdateCommentWithoutPassingContent(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->commentsUpdateRoute);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testCannotUpdateCommentWithSingleLetterLengthContent(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->commentsUpdateRoute, [
                'content' => 'S',
            ]);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testCannotUpdateCommentWithTooLongContent(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->commentsUpdateRoute, [
                'content' => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
            ]);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testResponseIncludesUpdatedComment(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->commentsUpdateRoute, [
                'content' => 'Simple updated comment',
            ]);

        $response->assertOk()
            ->assertJsonFragment([
                'content' => 'Simple updated comment',
            ]);
    }

    public function testPassedEmptyStringValueIsTreatingAsNullValue(): void
    {
        $response = $this->actingAs($this->user)
            ->putJson($this->commentsUpdateRoute, [
                'content' => '',
            ]);

        $response->assertJsonValidationErrorFor('content');
    }

    public function testCannotUpdateCommentWhichNotExists(): void
    {
        $route = route('api.comments.posts.update', [
            'resourceId' => $this->post->id,
            'comment' => 99999,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson($route, [
                'content' => 'Simple updated comment',
            ]);

        $response->assertNotFound();
    }
}
