<?php

declare(strict_types=1);

namespace Tests\Feature\Posts\Saved;

use App\Models\Post;
use App\Models\Saved;
use App\Models\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private User $user;

    private string $table = 'saved_posts';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    private function getRoute(Post | int $post): string
    {
        return route('api.saved.destroy', [
            'post' => $post,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $post = Post::factory()->createOne();

        $response = $this->deleteJson($this->getRoute($post));
        $response->assertUnauthorized();
    }

    public function testCanUnsavePostWhichIsSaveByLoggedUser(): void
    {
        $savedPost = Saved::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson($this->getRoute($savedPost->post));
        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseCount('posts', 1);
    }

    public function testCannotUnsavePostWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)->deleteJson($this->getRoute(99999));
        $response->assertNotFound();
    }

    public function testCannotUnsavePostWhichIsSavedByAnotherUser(): void
    {
        $savedPost = Saved::factory()->createOne();

        $response = $this->actingAs($this->user)->deleteJson($this->getRoute($savedPost->post));
        $response->assertNotFound();

        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotUnsavePostWhichExistsButIsNotSaved(): void
    {
        $post = Post::factory()->createOne();

        $response = $this->actingAs($this->user)->deleteJson($this->getRoute($post));
        $response->assertNotFound();

        $this->assertDatabaseCount($this->table, 0);
    }
}
