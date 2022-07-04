<?php

declare(strict_types=1);

namespace Tests\Feature\Comments\Posts;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private User $user;
    private Post $post;
    private Comment $comment;

    private string $commentsDestroyRoute;

    private string $commentsTable = 'comments';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->post = Post::factory()->createOne();
        $this->comment = Comment::factory()->createOne([
            'author_id' => $this->user->id,
            'resource_id' => $this->post->id,
        ]);

        $this->commentsDestroyRoute = route('api.comments.posts.destroy', [
            'resourceId' => $this->post->id,
            'comment' => $this->comment,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->deleteJson($this->commentsDestroyRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson($this->commentsDestroyRoute);

        $response->assertNoContent();
    }

    public function testCanDestroyOwnComment(): void
    {
        $this->assertDatabaseCount($this->commentsTable, 1);

        $response = $this->actingAs($this->user)
            ->deleteJson($this->commentsDestroyRoute);

        $response->assertNoContent();

        $this->assertDatabaseCount($this->commentsTable, 0);
    }

    public function testCannotDestroySomebodysComment(): void
    {
        $comment = Comment::factory()->createOne();

        $route = route('api.comments.posts.destroy', [
            'resourceId' => $this->post->id,
            'comment' => $comment,
        ]);

        $response = $this->actingAs($this->user)->deleteJson($route);

        $response->assertForbidden();

        $this->assertDatabaseCount($this->commentsTable, 2);
    }

    public function testCannotDestroyCommentWhichNotExists(): void
    {
        $route = route('api.comments.posts.destroy', [
            'resourceId' => $this->post->id,
            'comment' => 99999,
        ]);

        $response = $this->actingAs($this->user)->deleteJson($route);
        $response->assertNotFound();
    }
}
