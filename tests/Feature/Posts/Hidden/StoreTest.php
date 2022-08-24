<?php

declare(strict_types=1);

namespace Tests\Feature\Posts\Hidden;

use App\Models\HiddenPost;
use App\Models\Post;
use App\Models\SavedPost;
use App\Models\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private User $user;

    private string $route;

    private string $table = 'hidden_posts';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.hidden.posts.store');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->postJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanHideSomebodysPost(): void
    {
        $post = Post::factory()->createOne();

        $response = $this->actingAs($this->user)->postJson($this->route, [
            'post_id' => $post->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->table, 1)
            ->assertDatabaseHas($this->table, [
                'user_id' => $this->user->id,
                'post_id' => $post->id,
            ]);
    }

    public function testCannotHideSomebodysPostSecondTime(): void
    {
        $post = Post::factory()->createOne();

        HiddenPost::factory()->createOne([
            'user_id' => $this->user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->route, [
            'post_id' => $post->id,
        ]);

        $response->assertJsonValidationErrorFor('post_id');
        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotHideSomebodysPostWhichIsSaved(): void
    {
        $post = Post::factory()->createOne();

        SavedPost::factory()->createOne([
            'user_id' => $this->user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->route, [
            'post_id' => $post->id,
        ]);

        $response->assertJsonValidationErrorFor('post_id');
        $this->assertDatabaseCount($this->table, 0);
    }

    public function testCanHideSomebodysPostWhichIsSavedByAnotherUser(): void
    {
        $post = Post::factory()->createOne();

        SavedPost::factory()->createOne([
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->route, [
            'post_id' => $post->id,
        ]);

        $response->assertCreated();
        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotPassNoPostId(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->route);
        $response->assertJsonValidationErrorFor('post_id');
    }

    public function testCannotPassPostIdAsEmptyString(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->route, [
            'post_id' => '',
        ]);

        $response->assertJsonValidationErrorFor('post_id');
    }

    public function testCannotHidePostWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)->postJson($this->route, [
            'post_id' => 99999,
        ]);

        $response->assertNotFound();
    }

    public function testCannotHideOwnPost(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->postJson($this->route, [
            'post_id' => $post->id,
        ]);

        $response->assertJsonValidationErrorFor('post_id');
    }
}
