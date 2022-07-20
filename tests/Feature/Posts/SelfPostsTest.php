<?php

declare(strict_types=1);

namespace Tests\Feature\Posts;

use App\Models\Comment;
use App\Models\HiddenPost;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class SelfPostsTest extends TestCase
{
    private User $user;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.posts.self');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk();
    }

    public function testCanReturnProperlyAmountOfPosts(): void
    {
        Post::factory(4)->create([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(4);
    }

    public function testCanReturnMaxTenPosts(): void
    {
        Post::factory(12)->create([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(10);
    }

    public function testCanFetchMorePostsOnSecondPage(): void
    {
        Post::factory(13)->create([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->route.'?page=2');

        $response->assertOk()
            ->assertJsonCount(3);
    }

    public function testCanReturnEmptyResponseWhenNoPosts(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson($this->route.'?page=1');

        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function testReturnProperlyLikesAndCommentsStats(): void
    {
        $post = Post::factory()->create([
            'author_id' => $this->user->id,
        ]);

        $comments = Comment::factory(7)->create([
            'resource' => 'POST',
            'resource_id' => $post->id,
        ]);

        $likes = Like::factory(5)->create([
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonFragment([
                'likes_count' => $likes->count(),
                'comments_count' => $comments->count(),
            ]);
    }

    public function testReturnProperlyDataWhenPostIsLikedByLoggedUser(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        Like::factory()->createOne([
            'user_id' => $this->user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonFragment([
                'isLiked' => true,
            ]);
    }

    public function testReturnProperlyDataWhenPostIsNotLikedByLoggedUser(): void
    {
        Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonFragment([
                'isLiked' => false,
            ]);
    }

    public function testResponseNotContainFriendsPosts(): void
    {
        Post::factory(1)
            ->friendsAuthors($this->user->id)
            ->create();

        Post::factory(2)->create([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(2);
    }

    public function testResponseNotContainForeingUsersPosts(): void
    {
        Post::factory(3)->create();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function testCannotReturnHiddenPosts(): void
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        HiddenPost::factory()->createOne([
            'user_id' => $this->user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0);
    }
}
