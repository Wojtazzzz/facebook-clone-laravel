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

    private string $route;

    private string $table = 'comments';

    private function getRoute(Post | int $post, Comment | int $comment): string
    {
        return route('api.posts.comments.destroy', [
            'post' => $post,
            'comment' => $comment,
        ]);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->post = Post::factory()->createOne();
        $this->comment = Comment::factory()->createOne([
            'author_id' => $this->user->id,
            'resource_id' => $this->post->id,
        ]);

        $this->route = $this->getRoute($this->post, $this->comment);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->deleteJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)
            ->deleteJson($this->route);

        $response->assertNoContent();
    }

    public function testCanDestroyOwnComment(): void
    {
        $this->assertDatabaseCount($this->table, 1);

        $response = $this->actingAs($this->user)
            ->deleteJson($this->route);

        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotDestroySomebodysComment(): void
    {
        $comment = Comment::factory()->createOne();

        $route = $this->getRoute($this->post, $comment);

        $response = $this->actingAs($this->user)->deleteJson($route);
        $response->assertForbidden();

        $this->assertDatabaseCount($this->table, 2);
    }

    public function testCannotDestroyCommentWhichNotExists(): void
    {
        $route = $this->getRoute($this->post, 99999);

        $response = $this->actingAs($this->user)->deleteJson($route);
        $response->assertNotFound();
    }
}
