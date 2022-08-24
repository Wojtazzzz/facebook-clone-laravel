<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications;

use App\Models\Notification;
use App\Models\User;
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
        $this->route = route('api.notifications.markAsRead');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->putJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanUseAsAuthorized(): void
    {
        $response = $this->actingAs($this->user)->putJson($this->route);
        $response->assertNoContent();
    }

    public function testCanMarkAsReadAllNotifications(): void
    {
        Notification::factory(50)->create([
            'notifiable_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->putJson($this->route);
        $response->assertNoContent();

        $this->assertDatabaseMissing($this->table, [
            'read_at' => null,
        ]);
    }

    public function testWorksWhenUserHasNotNotifications(): void
    {
        $response = $this->actingAs($this->user)->putJson($this->route);
        $response->assertNoContent();
    }
}
