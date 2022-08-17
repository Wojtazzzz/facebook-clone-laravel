<?php

declare(strict_types=1);

namespace Tests\Feature\Posts;

use App\Enums\FriendshipStatus;
use App\Enums\PostType;
use App\Models\Comment;
use App\Models\Friendship;
use App\Models\HiddenPost;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.posts.index');
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
        Post::factory(6)->create([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()
            ->assertJsonCount(6, 'data');
    }

    public function testCanReturnMaxTenPosts(): void
    {
        Post::factory(12)->create([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()
            ->assertJsonCount(10, 'data');
    }

    public function testCanFetchMorePostsOnSecondPage(): void
    {
        Post::factory(13)->create([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson($this->route.'?page=2');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function testCanReturnEmptyResponseWhenNoPosts(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson($this->route);

        $response->assertOk()
            ->assertJsonCount(0, 'data');
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
            'likeable_id' => $post->id,
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
            'likeable_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonFragment([
                'is_liked' => true,
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
                'is_liked' => false,
            ]);
    }

    public function testCanReturnOwnPosts(): void
    {
        Post::factory(3)->create([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function testCannotReturnPostsWhichAuthorsAreNotFriends(): void
    {
        Post::factory(3)->create();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function testCanReturnPostsWhichAuthorsAreFriends(): void
    {
        Post::factory(5)
            ->friendsAuthors($this->user->id)
            ->create();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(5, 'data');
    }

    public function testCannotReturnHiddenPosts(): void
    {
        $posts = Post::factory(2)
            ->friendsAuthors($this->user->id)
            ->create();

        HiddenPost::factory()->createOne([
            'user_id' => $this->user->id,
            'post_id' => $posts[0]->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'id' => $posts[1]->id,
            ])
            ->assertJsonMissing([
                'id' => $posts[0]->id,
            ]);
    }

    public function testCanReturnPostsWhichHideFriend(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        HiddenPost::factory()->createOne([
            'user_id' => $friendship->friend_id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function testOwnPostHasOwnType(): void
    {
        Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'type' => PostType::OWN,
            ]);
    }

    public function testFriendPostHasOwnType(): void
    {
        $friendship = Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Post::factory()->createOne([
            'author_id' => $friendship->friend_id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'type' => PostType::FRIEND,
            ]);
    }

    public function testEditedPostHasProperlyIsEditedValue(): void
    {
        Post::factory()->createOne([
            'author_id' => $this->user->id,
            'created_at' => Carbon::yesterday(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'is_edited' => true,
            ]);
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasOnlyFirstPage(): void
    {
        Post::factory(4)->create([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonFragment([
                'current_page' => 1,
                'next_page' => null,
                'prev_page' => null,
            ]);
    }

    public function testFirstPageReturnProperlyPaginationDataWhenResourceHasSecondPage(): void
    {
        Post::factory(12)->create([
            'author_id' => $this->user->id,
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
        Post::factory(12)->create([
            'author_id' => $this->user->id,
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
