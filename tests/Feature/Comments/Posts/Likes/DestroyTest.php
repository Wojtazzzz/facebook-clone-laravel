<?php

declare(strict_types=1);

namespace Tests\Feature\Comments\Posts\Likes;

use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private User $user;

    private Comment $comment;

    private string $table = 'likes';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->comment = Comment::factory()->createOne();
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->deleteJson($this->getRoute($this->comment));
        $response->assertUnauthorized();
    }

    public function testCanDeleteOnlyOwnLike(): void
    {
        $comment = Comment::factory()->createOne();

        // Like to delete
        $this->comment->likes()->save(
            Like::factory()->createOne([
                'user_id' => $this->user->id,
            ])
        );

        // Random like on same comment
        $this->comment->likes()->save(
            Like::factory()->createOne()
        );

        // Own like on another comment
        $comment->likes()->save(
            Like::factory()->createOne([
                'user_id' => $this->user->id,
            ])
        );

        $response = $this->actingAs($this->user)->deleteJson($this->getRoute($this->comment));
        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 2);
    }

    public function testCannotDeleteSomebodysLike(): void
    {
        $friend = User::factory()->createOne();

        Like::factory()->createOne([
            'user_id' => $friend->id,
            'likeable_id' => $this->comment->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson($this->getRoute($this->comment));
        $response->assertJsonValidationErrorFor('comment');

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotDeleteLikeWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)->deleteJson($this->getRoute($this->comment));
        $response->assertJsonValidationErrorFor('comment');

        $this->assertDatabaseCount($this->table, 0);
    }

    private function getRoute(Comment | int $comment): string
    {
        return route('api.comments.likes.store', [
            'comment' => $comment,
        ]);
    }
}
