<?php

declare(strict_types=1);

namespace Tests\Feature\Posts;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class ToggleCommentingTest extends TestCase
{
    private User $user;

    private string $table = 'posts';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    private function getRoute(Post $post): string
    {
        return route('api.posts.commenting.update', [
            'post' => $post,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $post = Post::factory()->createOne();

        $response = $this->putJson($this->getRoute($post));
        $response->assertUnauthorized();
    }

    public function testCanTurnOffCommentsOnOwnPost(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->putJson($this->getRoute($post));
        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'commenting' => false,
            ]);
    }

    public function testCanTurnOnCommentsOnOwnPost(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
            'commenting' => false,
        ]);

        $response = $this->actingAs($this->user)->putJson($this->getRoute($post));
        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'commenting' => true,
            ]);
    }

    public function testCannotTurnOffCommentsOnFriendsPost(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $post = Post::factory()->createOne([
            'author_id' => $friendship->friend_id,
        ]);

        $response = $this->actingAs($this->user)->putJson($this->getRoute($post));
        $response->assertForbidden();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'commenting' => true,
            ]);
    }

    public function testCannotTurnOnCommentsOnFriendsPost(): void
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
        $post = Post::factory()->createOne();

        $response = $this->actingAs($this->user)->putJson($this->getRoute($post));
        $response->assertForbidden();

        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'commenting' => true,
            ]);
    }

    public function testCannotTurnOnCommentsOnRandomUserPost(): void
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
}
