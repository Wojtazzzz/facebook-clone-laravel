<?php

declare(strict_types=1);

namespace Tests\Feature\Posts;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class TurnOnCommentsTest extends TestCase
{
    private User $user;

    private string $table = 'posts';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $post = Post::factory()->createOne();

        $response = $this->putJson($this->getRoute($post));
        $response->assertUnauthorized();
    }

    public function testCanTurnOnCommentsOnOwnPost(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'commenting' => false,
        ]);

        $response = $this->actingAs($this->user)->putJson($this->getRoute($post));
        $response->assertOk();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'commenting' => true,
            ]);
    }

    public function testTurnOnCommentsOnPostWhichAlreadyHasThisOptionOffNotThrowError(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'commenting' => true,
        ]);

        $response = $this->actingAs($this->user)->putJson($this->getRoute($post));
        $response->assertOk();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'commenting' => true,
            ]);
    }

    public function testCannotTurnOonCommentsOnFriendsPost(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $post = Post::factory()->createOne([
            'author_id' => $friendship->friend_id,
            'commenting' => false,
        ]);

        $response = $this->actingAs($this->user)->putJson($this->getRoute($post));
        $response->assertForbidden();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'commenting' => false,
            ]);
    }

    public function testCannotTurnOffCommentsOnRandomUserPost(): void
    {
        $post = Post::factory()->createOne([
            'commenting' => false,
        ]);

        $response = $this->actingAs($this->user)->putJson($this->getRoute($post));
        $response->assertForbidden();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'commenting' => false,
            ]);
    }

    private function getRoute(Post $post): string
    {
        return route('api.posts.turnOnComments', [
            'post' => $post,
        ]);
    }
}
