<?php

declare(strict_types=1);

namespace Tests\Feature\Comments\Posts\Likes;

use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use App\Notifications\CommentLiked;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    private Comment $comment;

    private string $route;

    private string $table = 'likes';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->comment = Comment::factory()->createOne();

        $this->route = route('api.comments.likes.store', [
            'comment' => $this->comment,
        ]);

        Notification::fake();
    }

    private function getRoute(Comment | int $comment): string
    {
        return route('api.comments.likes.store', [
            'comment' => $comment,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->postJson($this->getRoute($this->comment));
        $response->assertUnauthorized();
    }

    public function testCannotCreateLikeForCommentWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->getRoute(99999));

        $response->assertNotFound();
        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCannotCreateLikeForCommentWhichIsAlreadyLiked(): void
    {
        $this->comment->likes()->save(new Like([
            'user_id' => $this->user->id,
        ]));

        $response = $this->actingAs($this->user)->postJson($this->route);
        $response->assertJsonValidationErrorFor('comment');

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCanLikeComment(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->route);
        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCanLikeCommentWhichIsLikedByAnotherUser(): void
    {
        $this->comment->likes()->save(Like::factory()->createOne());

        $response = $this->actingAs($this->user)->postJson($this->route);
        $response->assertCreated();
    }

    public function testLikeCommentSendNotificationToCommentAuthor(): void
    {
        $author = User::factory()->createOne();

        $comment = Comment::factory()->createOne([
            'author_id' => $author->id
        ]);

        $response = $this->actingAs($this->user)->postJson($this->getRoute($comment));
        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1);

        Notification::assertSentTo($author, CommentLiked::class);
        Notification::assertTimesSent(1, CommentLiked::class);
    }

    public function testLikeActionNotSendNotificationWhenLikeOwnComment(): void
    {
        $comment = Comment::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->getRoute($comment));
        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1);

        Notification::assertNotSentTo($this->user, CommentLiked::class);
        Notification::assertTimesSent(0, CommentLiked::class);
    }
}
