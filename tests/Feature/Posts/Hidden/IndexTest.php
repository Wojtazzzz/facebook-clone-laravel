<?php

declare(strict_types=1);

namespace Tests\Feature\Posts\Hidden;

use App\Models\Comment;
use App\Models\Hidden;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.hidden.index');
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
            ->assertJsonCount(0, 'data');
    }

    public function testCannotReturnUnhiddenFriendPosts(): void
    {
        Post::factory()
            ->friendsAuthors($this->user->id)
            ->createOne();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testCannotReturnUnhiddenForeingUsersPosts(): void
    {
        Post::factory()->createOne();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testReturnProperlyCountOfOwnHiddenPosts(): void
    {
        Hidden::factory(5)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(5, 'data');
    }

    public function testReturnMaxTenPosts(): void
    {
        Hidden::factory(12)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(10, 'data');
    }

    public function testCanFetchMorePostsFromSecondPage(): void
    {
        Hidden::factory(12)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');
        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testCannotReturnHiddenFriendsPosts(): void
    {
        $post = Post::factory()
            ->createOne([
                'author_id' => $this->user->id,
            ]);

        Hidden::factory()->createOne([
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testCannotReturnHiddenForeingUsersPosts(): void
    {
        Hidden::factory()->createOne();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testPostsInResponseContainsProperlyPostAuthor(): void
    {
        $hiddenPost = Hidden::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        $post = Post::with('author')->findOrFail($hiddenPost->post_id);
        $author = $post->author;

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'author' => [
                    'background_image' => $author->background_image,
                    'first_name' => $author->first_name,
                    'id' => $author->id,
                    'name' => $author->name,
                    'profile_image' => $author->profile_image,
                ],
            ]);
    }

    public function testHiddenPostHasIsHiddenPropertyToTrue(): void
    {
        Hidden::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'is_hidden' => true,
            ]);
    }

    public function testPostsInResponseContainsProperlyLikesCountAndZeroCommentsCount(): void
    {
        $hiddenPost = Hidden::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        Like::factory()->createOne([
            'likeable_id' => $hiddenPost->post_id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'likes_count' => 1,
                'comments_count' => 0,
            ]);
    }

    public function testPostsInResponseContainsProperlyCommentsCountAndZeroLikesCount(): void
    {
        $hiddenPost = Hidden::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        Comment::factory()
            ->forPost($hiddenPost->post_id)
            ->createOne();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'comments_count' => 1,
                'likes_count' => 0,
            ]);
    }

    public function testPostsInResponseContainsProperlyIsLikedValue(): void
    {
        $hiddenPost = Hidden::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        Like::factory()->createOne([
            'user_id' => $this->user->id,
            'likeable_id' => $hiddenPost->post_id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'is_liked' => true,
            ]);
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasOnlyFirstPage(): void
    {
        Hidden::factory(2)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => null,
                'prev_page' => null,
            ]);
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasSecondPage(): void
    {
        Hidden::factory(12)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => 2,
                'prev_page' => null,
            ]);
    }

    public function testSecondPageReturnProperlyPaginationData(): void
    {
        Hidden::factory(12)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment([
                'current_page' => 2,
                'next_page' => null,
                'prev_page' => 1,
            ]);
    }
}
