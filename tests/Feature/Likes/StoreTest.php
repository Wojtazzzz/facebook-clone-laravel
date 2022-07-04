<?php

declare(strict_types=1);

namespace Tests\Feature\Likes;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    private string $likesStoreRoute;

    private string $likesTable = 'likes';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->likesStoreRoute = route('api.likes.store');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->postJson($this->likesStoreRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $post = Post::factory()->createOne();

        $response = $this->actingAs($this->user)
            ->postJson($this->likesStoreRoute, [
                'post_id' => $post->id,
            ]);

        $response->assertCreated();
    }

    public function testPassedEmptyValueIsTreatingAsNullValue(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->likesStoreRoute, [
                'post_id' => '',
            ]);

        $response->assertJsonValidationErrorFor('post_id');
    }

    public function testCannotCreateLikeForPostWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson($this->likesStoreRoute, [
                'post_id' => 99999,
            ]);

        $response->assertUnprocessable();

        $this->assertDatabaseCount($this->likesTable, 0);
    }

    public function testCannotCreateLikeForPostWhichIsAlreadyLikedByLoggedUser(): void
    {
        $post = Post::factory()->createOne();

        Like::factory()->createOne([
            'user_id' => $this->user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->likesStoreRoute, [
            'post_id' => $post->id,
        ]);

        $response->assertJsonValidationErrorFor('post_id');

        $this->assertDatabaseCount($this->likesTable, 1);
    }

    public function testCanCreateLike(): void
    {
        $post = Post::factory()->createOne();

        $response = $this->actingAs($this->user)
            ->postJson($this->likesStoreRoute, [
                'post_id' => $post->id,
            ]);

        $response->assertCreated();

        $this->assertDatabaseCount($this->likesTable, 1);
    }

    public function testCannotPassNoPostId(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->likesStoreRoute);
        $response->assertJsonValidationErrorFor('post_id');
    }

    public function testCanLikePostWhichIsLikedByAnotherUser(): void
    {
        $post = Post::factory()->createOne();

        Like::factory(2)->create([
            'post_id' => $post->id,
        ]);
        
        $response = $this->actingAs($this->user)
            ->postJson($this->likesStoreRoute, [
                'post_id' => $post->id,
            ]);

        $response->assertCreated();
    }

    public function testResponseHasProperlyLikesCount(): void
    {
        $post = Post::factory()->createOne();

        Like::factory(2)->create([
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson($this->likesStoreRoute, [
                'post_id' => $post->id,
            ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'data' => [
                    'likesCount' => 3,
                ],
            ]);
    }
}
