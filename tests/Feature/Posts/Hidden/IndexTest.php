<?php

declare(strict_types=1);

namespace Tests\Feature\Posts\Hidden;

use App\Models\Comment;
use App\Models\HiddenPost;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    private string $route;
    private string $table = 'hidden_posts';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.hidden.posts.index');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCannotReturnUnhiddenOwnPosts(): void
    {
        Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function testCannotReturnUnhiddenFriendPosts(): void
    {
        Post::factory()
            ->friendsAuthors($this->user->id)
            ->createOne();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function testCannotReturnUnhiddenForeingUsersPosts(): void
    {
        Post::factory()->createOne();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function testReturnProperlyCountOfOwnHiddenPosts(): void
    {
        HiddenPost::factory(5)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(5);
    }

    public function testReturnMaxTenPosts(): void
    {
        HiddenPost::factory(12)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(10);
    }

    public function testCanFetchMorePostsFromSecondPage(): void
    {
        HiddenPost::factory(12)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');
        $response->assertOk()
            ->assertJsonCount(2);
    }

    public function testCannotReturnHiddenFriendsPosts(): void
    {
        $post = Post::factory()
            ->createOne([
                'author_id' => $this->user->id,
            ]);

        HiddenPost::factory()->createOne([
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function testCannotReturnHiddenForeingUsersPosts(): void
    {
        HiddenPost::factory()->createOne();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function testPostsInResponseContainsProperlyPostAuthor(): void
    {
        $hiddenPost = HiddenPost::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        $post = Post::with('author')->findOrFail($hiddenPost->post_id);
        $author = $post->author;

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'author' => [
                    'background_image' => $author->background_image,
                    'first_name' => $author->first_name,
                    'id' => $author->id,
                    'name' => "$author->first_name $author->last_name",
                    'profile_image' => $author->profile_image,
                ],
            ]);
    }

    public function testPostsInResponseContainsProperlyLikesCountAndZeroCommentsCount(): void
    {
        $hiddenPost = HiddenPost::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        Like::factory()->createOne([
            'post_id' => $hiddenPost->post_id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'likes_count' => 1,
                'comments_count' => 0,
            ]);
    }

    public function testPostsInResponseContainsProperlyCommentsCountAndZeroLikesCount(): void
    {
        $hiddenPost = HiddenPost::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        Comment::factory()->createOne([
            'resource_id' => $hiddenPost->post_id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'comments_count' => 1,
                'likes_count' => 0,
            ]);
    }

    public function testPostsInResponseContainsProperlyIsLikedValue(): void
    {
        $hiddenPost = HiddenPost::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        Like::factory()->createOne([
            'user_id' => $this->user->id,
            'post_id' => $hiddenPost->post_id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'isLiked' => true,
            ]);
    }
}
