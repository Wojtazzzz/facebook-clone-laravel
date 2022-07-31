<?php

declare(strict_types=1);

namespace Tests\Feature\Likes;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private User $user;
    private Post $post;

    private string $route;
    private string $table = 'likes';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->post = Post::factory()->createOne();
        $this->route = route('api.posts.likes.destroy', $this->post->id);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->deleteJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanDeleteOnlyOwnSpecificedLike(): void
    {
        $post = Post::factory()->createOne();

        Like::factory()->createOne([
            'user_id' => $this->user->id,
            'likeable_id' => $this->post->id,
        ]);

        Like::factory()->createOne([
            'user_id' => $this->user->id,
            'likeable_id' => $post->id,
        ]);

        Like::factory()->createOne([
            'likeable_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson($this->route);
        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 2);
    }

    public function testCannotDeleteSomebodysLike(): void
    {
        $friend = User::factory()->createOne();

        Like::factory()->createOne([
            'user_id' => $friend->id,
            'likeable_id' => $this->post->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson($this->route);
        $response->assertJsonValidationErrorFor('post');

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotDeleteLikeWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)->deleteJson($this->route);
        $response->assertJsonValidationErrorFor('post');

        $this->assertDatabaseCount($this->table, 0);
    }
}
