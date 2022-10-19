<?php

declare(strict_types=1);

namespace Tests\Feature\Posts\Saved;

use App\Enums\FriendshipStatus;
use App\Models\Comment;
use App\Models\Friendship;
use App\Models\Like;
use App\Models\Post;
use App\Models\Saved;
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
        $this->route = route('api.saved.index');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->route);
        $response->assertUnauthorized();
    }

    public function testReturnOnlyOwnSavedPost(): void
    {
        // Own saved posts
        Saved::factory(2)->create([
            'user_id' => $this->user->id,
        ]);

        // Foreing users saved posts
        Saved::factory(3)->create();

        // Friends saved posts
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Saved::factory()->createOne([
            'user_id' => $friendship->friend_id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testReturnMaxTenPosts(): void
    {
        Saved::factory(12)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(10, 'data');
    }

    public function testCanFetchMorePostsFromSecondPage(): void
    {
        Saved::factory(12)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route.'?page=2');
        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function testPostsInResponseContainsProperlyPostAuthor(): void
    {
        $savedPost = Saved::factory()->createOne([
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
                    'name' => $author->name,
                    'profile_image' => $author->profile_image,
                ],
            ]);
    }

    public function testPostsInResponseContainsProperlyPostType(): void
    {
        Saved::factory()->createOne([
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
        $savedPost = Saved::factory()->createOne([
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
        $savedPost = Saved::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        Comment::factory()
            ->forPost($savedPost->post_id)
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
        $savedPost = Saved::factory()->createOne([
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
        Saved::factory(2)->create([
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
        Saved::factory(12)->create([
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
        Saved::factory(12)->create([
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
