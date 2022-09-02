<?php

declare(strict_types=1);

namespace Tests\Feature\Posts\Saved;

use App\Enums\FriendshipStatus;
use App\Models\Comment;
use App\Models\Friendship;
use App\Models\Like;
use App\Models\Post;
use App\Models\SavedPost;
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
        $this->route = route('api.saved.posts.index');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->route);
        $response->assertUnauthorized();
    }

    public function testReturnOnlyOwnSavedPost(): void
    {
        // Own saved posts
        SavedPost::factory(2)->create([
            'user_id' => $this->user->id,
        ]);

        // Foreing users saved posts
        SavedPost::factory(3)->create();

        // Friends saved posts
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        SavedPost::factory()->createOne([
            'user_id' => $friendship->friend_id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testReturnMaxTenPosts(): void
    {
        SavedPost::factory(12)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(10, 'data');
    }

    public function testCanFetchMorePostsFromSecondPage(): void
    {
        SavedPost::factory(12)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');
        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testPostsInResponseContainsProperlyPostAuthor(): void
    {
        $savedPost = SavedPost::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        $post = Post::with('author')->findOrFail($savedPost->post_id);
        $author = $post->author;

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
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

    public function testPostsInResponseContainsProperlyPostType(): void
    {
        SavedPost::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'is_saved' => true,
            ]);
    }

    public function testPostsInResponseContainsProperlyLikesCountAndZeroCommentsCount(): void
    {
        $savedPost = SavedPost::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        Like::factory()->createOne([
            'likeable_id' => $savedPost->post_id,
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
        $savedPost = SavedPost::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        Comment::factory()->createOne([
            'resource_id' => $savedPost->post_id,
        ]);

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
        $savedPost = SavedPost::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        Like::factory()->createOne([
            'user_id' => $this->user->id,
            'likeable_id' => $savedPost->post_id,
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
        SavedPost::factory(2)->create([
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
        SavedPost::factory(12)->create([
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
        SavedPost::factory(12)->create([
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
