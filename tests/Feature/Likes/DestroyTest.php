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

    private string $likesDestroyRoute;

    private string $likesTable = 'likes';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->post = Post::factory()->createOne();
        $this->likesDestroyRoute = route('api.likes.destroy', $this->post->id);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->deleteJson($this->likesDestroyRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $this->generateLike();

        $response = $this->actingAs($this->user)->deleteJson($this->likesDestroyRoute);
        $response->assertOk();
    }

    public function testCanDeleteOwnLike(): void
    {
        $this->generateLike();

        $response = $this->actingAs($this->user)->deleteJson($this->likesDestroyRoute);

        $response->assertOk();
        $this->assertDatabaseCount($this->likesTable, 0);
    }

    public function testCannotDeleteSomebodysLike(): void
    {
        $friend = User::factory()->createOne();

        $this->generateLike($friend->id);

        $response = $this->actingAs($this->user)->deleteJson($this->likesDestroyRoute);

        $response->assertNotFound();
        $this->assertDatabaseCount($this->likesTable, 1);
    }

    public function testCannotDeleteLikeWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)->deleteJson($this->likesDestroyRoute);

        $response->assertNotFound();
        $this->assertDatabaseCount($this->likesTable, 0);
    }

    public function testResponseHasProperlyLikesCount(): void
    {
        $friends = User::factory(2)->create();

        $this->generateLike($friends[0]->id);
        $this->generateLike($friends[1]->id);
        $this->generateLike();

        $response = $this->actingAs($this->user)->deleteJson($this->likesDestroyRoute);

        $response->assertOk()
            ->assertJsonFragment([
                'data' => [
                    'likesCount' => 2,
                ],
            ]);
    }

    private function generateLike(int $userId = null): void
    {
        Like::factory()->createOne([
            'user_id' => $userId ?? $this->user->id,
            'post_id' => $this->post->id,
        ]);
    }
}
