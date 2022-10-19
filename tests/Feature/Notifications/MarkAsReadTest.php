<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications;

use App\Models\Post;
use App\Models\User;
use App\Notifications\PostLiked;
use Tests\TestCase;

class MarkAsReadTest extends TestCase
{
    private User $user;

    private string $route;

    private string $table = 'notifications';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createOne();
        $this->route = route('api.notifications.update');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->putJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanMarkAsReadPassedNotifications(): void
    {
        $this->generateNotifications(4);

        $response = $this->actingAs($this->user)->putJson($this->route, [
            'ids' => $this->user->unreadNotifications->pluck('id'),
        ]);

        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 4)
            ->assertDatabaseMissing($this->table, [
                'read_at' => null,
            ]);
    }

    public function testMarkOnlyPassedNotifications(): void
    {
        $this->generateNotifications(8);

        $response = $this->actingAs($this->user)->putJson($this->route, [
            'ids' => $this->user->unreadNotifications->pluck('id')->except(0),
        ]);

        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 8)
            ->assertDatabaseHas($this->table, [
                'read_at' => null,
            ]);

        $unreadNotifications = $this->user->unreadNotifications()->whereNull('read_at')->get();

        $this->assertCount(1, $unreadNotifications);
    }

    public function testCannotPassEmptyArrayOfIds(): void
    {
        $response = $this->actingAs($this->user)->putJson($this->route, [
            'ids' => [],
        ]);

        $response->assertJsonValidationErrorFor('ids');
    }

    public function testCannotNotPassArrayOfIds(): void
    {
        $response = $this->actingAs($this->user)->putJson($this->route);
        $response->assertJsonValidationErrorFor('ids');
    }

    public function testCannotPassUuidInsteadOfArray(): void
    {
        $this->generateNotifications(1);

        $response = $this->actingAs($this->user)->putJson($this->route, [
            'ids' => $this->user->notifications[0]->id,
        ]);

        $response->assertJsonValidationErrorFor('ids');
    }

    public function testIdInArrayMustBeUuid(): void
    {
        $response = $this->actingAs($this->user)->putJson($this->route, [
            'ids' => 'random-string',
        ]);

        $response->assertJsonValidationErrorFor('ids');
    }

    public function testNotThrowErrorWhenNotificationNotFound(): void
    {
        $response = $this->actingAs($this->user)->putJson($this->route, [
            'ids' => ['7aebcbc9-8775-3371-a895-77e5cd083a63'],
        ]);

        $response->assertNoContent();

        $this->assertDatabaseCount($this->table, 0);
    }

    private function generateNotifications(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $friend = User::factory()->createOne();
            $post = Post::factory()->createOne();

            $this->user->notify(new PostLiked($friend->id, $post));
        }
    }
}
