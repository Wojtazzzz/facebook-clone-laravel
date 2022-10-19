<?php

declare(strict_types=1);

namespace Tests\Feature\Posts\Hidden;

use App\Models\Hidden;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private User $user;

    private string $table = 'hidden_posts';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
    }

    private function getRoute(Post | int $post): string
    {
        return route('api.hidden.destroy', [
            'post' => $post,
        ]);
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $post = Post::factory()->createOne();

        $response = $this->deleteJson($this->getRoute($post));
        $response->assertUnauthorized();
    }

    public function testCanUnhidePostWhichIsHideByLoggedUser(): void
    {
        $hiddenPost = Hidden::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        $post = $hiddenPost->post;

        $response = $this->actingAs($this->user)->deleteJson($this->getRoute($post));
        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 0);
        $this->assertDatabaseCount('posts', 1);
    }

    public function testCannotUnhidePostWhichNotExists(): void
    {
        $response = $this->actingAs($this->user)->deleteJson($this->getRoute(99999));

        $response->assertNotFound();
    }

    public function testCannotUnhidePostWhichIsHiddenByAnotherUser(): void
    {
        $hiddenPost = Hidden::factory()->createOne();

        $response = $this->actingAs($this->user)->deleteJson($this->getRoute($hiddenPost->post));

        $response->assertNotFound();
        $this->assertDatabaseCount($this->table, 1);
    }

    public function testCannotUnhidePostWhichExistsButIsNotHidden(): void
    {
        $post = Post::factory()->createOne();

        $response = $this->actingAs($this->user)->deleteJson($this->getRoute($post));

        $response->assertNotFound();
        $this->assertDatabaseCount($this->table, 0);
    }
}
