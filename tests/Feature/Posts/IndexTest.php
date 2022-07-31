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
            ->assertJsonCount(6);
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
            ->getJson($this->route);

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

    public function testCanReturnOwnPosts(): void
    {
        Post::factory(3)->create([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(3);
    }

    public function testCannotReturnPostsWhichAuthorsAreNotFriends(): void
    {
        Post::factory(3)->create();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function testCanReturnPostsWhichAuthorsAreFriends(): void
    {
        Post::factory(5)
            ->friendsAuthors($this->user->id)
            ->create();

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(5);
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
            ->assertJsonCount(1)
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
            ->assertJsonCount(1);
    }

    public function testOwnPostHasOwnType(): void
    {
        Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()
            ->assertJsonCount(1)
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
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'type' => PostType::FRIEND,
            ]);
    }
}
