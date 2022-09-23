<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications;

use App\Models\Post;
use App\Models\User;
use App\Notifications\PostLiked;
use Tests\TestCase;

class CheckUnreadTest extends TestCase
{
    private User $user;

    private User $friend;

    private Post $post;

    private string $route;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->friend = User::factory()->createOne();
        $this->post = Post::factory()->createOne();
        $this->route = route('api.notifications.checkUnread');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->getJson($this->route);
        $response->assertUnauthorized();
    }

    public function testReturnTrueIfUserHasOneUnreadNotification(): void
    {
        $this->user->notify(new PostLiked($this->friend->id, $this->post));

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('true');
    }

    public function testReturnTrueIfUserHasMoreUnreadNotifications(): void
    {
        $this->user->notify(new PostLiked(User::factory()->create()->id, $this->post));
        $this->user->notify(new PostLiked(User::factory()->create()->id, $this->post));
        $this->user->notify(new PostLiked(User::factory()->create()->id, $this->post));

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('true');
    }

    public function testReturnTrueIfUserHasReadAndUnreadNotifications(): void
    {
        $this->user->notify(new PostLiked(User::factory()->create()->id, $this->post));
        $this->user->notify(new PostLiked(User::factory()->create()->id, $this->post));
        $this->user->notify(new PostLiked(User::factory()->create()->id, $this->post));
        $this->user->notify(new PostLiked(User::factory()->create()->id, $this->post));

        $this->user->unreadNotifications->take(2)->markAsRead();

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('true');
    }

    public function testReturnFalseIfUserDoesntHaveNotifications(): void
    {
        $response = $this->actingAs($this->user)->getJson($this->route);
        $response->assertOk()->assertSeeText('false');
    }

    public function testReturnFalseIfUserHasOneReadNotification(): void
    {
        $this->user->notify(new PostLiked(User::factory()->create()->id, $this->post));

        $this->user->unreadNotifications->markAsRead();

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('false');
    }

    public function testReturnFalseIfUserHasMoreReadNotifications(): void
    {
        $this->user->notify(new PostLiked(User::factory()->create()->id, $this->post));
        $this->user->notify(new PostLiked(User::factory()->create()->id, $this->post));
        $this->user->notify(new PostLiked(User::factory()->create()->id, $this->post));

        $this->user->unreadNotifications->markAsRead();

        $response = $this->actingAs($this->user)->getJson($this->route);

        $response->assertOk()->assertSeeText('false');
    }
}
