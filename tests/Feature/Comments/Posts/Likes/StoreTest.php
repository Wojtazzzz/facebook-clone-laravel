<?php

declare(strict_types=1);

namespace Tests\Feature\Comments\Posts\Likes;

use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    private Comment $comment;

    private string $route;

    private string $table = 'likes';

    private string $notificationsTable = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->comment = Comment::factory()->createOne();

        $this->route = route('api.comments.likes.store', [
            'comment' => $this->comment,
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
        $comment = Comment::factory()->createOne();

        $response = $this->actingAs($this->user)->postJson($this->getRoute($comment));
        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseCount($this->notificationsTable, 1)
            ->assertDatabaseHas($this->notificationsTable, [
                'notifiable_id' => $comment->author_id,
                'read_at' => null,
            ]);
    }

    public function testLikeActionNotSendNotificationIfThatNotificationAlreadyExists(): void
    {
        $comment = Comment::factory()->createOne();

        $response = $this->actingAs($this->user)->postJson($this->getRoute($comment));
        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseCount($this->notificationsTable, 1);

        Like::truncate();

        $response = $this->actingAs($this->user)->postJson($this->getRoute($comment));
        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseCount($this->notificationsTable, 1);
    }

    public function testLikeActionNotSendNotificationWhenLikeOwnComment(): void
    {
        $comment = Comment::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->getRoute($comment));
        $response->assertCreated();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseCount($this->notificationsTable, 0);
    }

    private function getRoute(Comment | int $comment): string
    {
        return route('api.comments.likes.store', [
            'comment' => $comment,
        ]);
    }
}
