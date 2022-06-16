<?php

namespace Tests\Feature\Posts;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private User $user;
    private Post $post;

    private string $postsDestroyRoute;

    private string $postsTable = 'posts';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);
        $this->postsDestroyRoute = route('api.posts.destroy', $this->post);
    }

    public function testCannotUseAsUnauthorized()
    {
        $response = $this->deleteJson($this->postsDestroyRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized()
    {
        $response = $this->actingAs($this->user)->deleteJson($this->postsDestroyRoute);
        $response->assertNoContent();
    }

    public function testCanDeleteOwnPost()
    {
        $response = $this->actingAs($this->user)->deleteJson($this->postsDestroyRoute);

        $response->assertNoContent();
        $this->assertDatabaseCount($this->postsTable, 0);
    }

    public function testCannotDeletePostWhichNotExist()
    {
        $response = $this->actingAs($this->user)->deleteJson(route('api.posts.destroy', 99999));
        $response->assertNotFound();
    }

    public function testCannotDeleteSomebodysPost()
    {
        $friend = User::factory()->createOne();
        $friendPost = Post::factory()->createOne([
            'author_id' => $friend->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson(route('api.posts.destroy', $friendPost));

        $response->assertForbidden();
        $this->assertDatabaseCount($this->postsTable, 2)
            ->assertDatabaseHas($this->postsTable, [
                'id' => $friendPost->id,
                'content' => $friendPost->content,
                'author_id' => $friendPost->author_id,
            ]);
    }
}
