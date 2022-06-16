<?php

namespace Tests\Feature\Posts;

use App\Enums\FriendshipStatus;
use App\Models\Comment;
use App\Models\Friendship;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private User $user;
    private Collection $friends;

    private string $postsIndexRoute;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->postsIndexRoute = route('api.posts.index');
    }

    public function testCannotUseAsUnauthorized()
    {
        $response = $this->getJson($this->postsIndexRoute);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized()
    {
        $response = $this->actingAs($this->user)->getJson($this->postsIndexRoute);
        $response->assertOk();
    }

    public function testCanReturnPosts()
    {
        Post::factory(6)->create();

        $response = $this->actingAs($this->user)->getJson($this->postsIndexRoute);

        $response->assertOk()
            ->assertJsonCount(6);
    }

    public function testCanReturnMaxTenPosts()
    {
        Post::factory(17)->create();

        $response = $this->actingAs($this->user)->getJson($this->postsIndexRoute);

        $response->assertOk()
            ->assertJsonCount(10);
    }

    public function testCanFetchMorePostsOnSecondPage()
    {
        Post::factory(17)->create();

        $response = $this->actingAs($this->user)->getJson($this->postsIndexRoute.'?page=2');

        $response->assertOk()
            ->assertJsonCount(7);
    }

    public function testCanReturnEmptyResponseWhenNoPosts()
    {
        $response = $this->actingAs($this->user)->getJson($this->postsIndexRoute.'?page=2');

        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function testReturnProperlyLikesAndCommentsStats()
    {
        $this->friends = User::factory(20)->create();

        $posts = Post::factory(2)->create([
            'author_id' => $this->user->id,
        ]);
        $comments = Comment::factory(7)->create([
            'resource' => 'POST',
            'resource_id' => $posts[1]->id,
        ]);
        $likes = Like::factory(5)->create([
            'user_id' => fn () => $this->faker->unique()->randomElement($this->friends->pluck('id')),
            'post_id' => $posts[1]->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->postsIndexRoute);

        $response->assertOk()
            ->assertJsonCount(2)
            ->assertJsonFragment([
                'likes_count' => $likes->count(),
                'comments_count' => $comments->count(),
            ]);
    }

    public function testReturnProperlyDataWhenPostIsLikedByLoggedUser()
    {
        $post = Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        Like::factory()->createOne([
            'user_id' => $this->user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->postsIndexRoute);

        $response->assertOk()
            ->assertJsonFragment([
                'isLiked' => true,
            ]);
    }

    public function testReturnProperlyDataWhenPostIsNotLikedByLoggedUser()
    {
        Post::factory()->createOne([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->postsIndexRoute);

        $response->assertOk()
            ->assertJsonFragment([
                'isLiked' => false,
            ]);
    }

    public function testCanReturnOwnPosts()
    {
        Post::factory(3)->create([
            'author_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson($this->postsIndexRoute);

        $response->assertOk()
            ->assertJsonCount(3);
    }

    public function testCannotReturnUsersPostsWhichAreNotFriends()
    {
        $this->friends = User::factory(5)->create();

        Post::factory(3)->create([
            'author_id' => fn () => $this->faker->unique->randomElement($this->friends->pluck('id')),
        ]);

        $response = $this->actingAs($this->user)->getJson($this->postsIndexRoute);

        $response->assertOk()
            ->assertJsonCount(0);
    }

    public function testCanReturnUsersPostsWhichAreFriends()
    {
        $this->friends = User::factory(10)->create();

        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friends[0],
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Friendship::factory()->createOne([
            'user_id' => $this->user->id,
            'friend_id' => $this->friends[1],
            'status' => FriendshipStatus::CONFIRMED,
        ]);

        Post::factory(5)->create([
            'author_id' => fn () => $this->faker->randomElement([$this->friends[0]->id, $this->friends[1]->id]),
        ]);

        $response = $this->actingAs($this->user)->getJson($this->postsIndexRoute);

        $response->assertOk()
            ->assertJsonCount(5);
    }
}
