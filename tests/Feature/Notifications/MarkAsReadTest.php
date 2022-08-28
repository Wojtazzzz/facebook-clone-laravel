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
        $this->route = route('api.notifications.update');
    }

    public function testCannotUseAsUnauthorized(): void
    {
        $response = $this->putJson($this->route);
        $response->assertUnauthorized();
    }

    public function testCanMarkAsReadPassedNotifications(): void
    {
        $notifications = Notification::factory(8)->create([
            'notifiable_id' => $this->user->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user)->putJson($this->route, [
            'ids' => $notifications->pluck('id'),
        ]);

        $response->assertOk();

        $this->assertDatabaseCount($this->table, 8)
            ->assertDatabaseMissing($this->table, [
                'read_at' => null,
            ]);
    }

    public function testMarkOnlyPassedNotifications(): void
    {
        $notifications = Notification::factory(8)->create([
            'notifiable_id' => $this->user->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user)->putJson($this->route, [
            'ids' => $notifications->pluck('id')->except(0),
        ]);

        $response->assertOk();

        $this->assertDatabaseCount($this->table, 8)
            ->assertDatabaseHas($this->table, [
                'read_at' => null,
            ]);

        $storedNotifications = Notification::get();

        $this->assertNull($storedNotifications[0]->read_at);
        $this->assertIsString($storedNotifications[1]->read_at);
        $this->assertIsString($storedNotifications[7]->read_at);
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
        $notification = Notification::factory()->createOne([
            'notifiable_id' => $this->user->id,
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->user)->putJson($this->route, [
            'ids' => $notification->id,
        ]);

        $response->assertJsonValidationErrorFor('ids');
    }

    public function testIdInArrayMustBeUuid(): void
    {
        Notification::factory()->createOne([
            'notifiable_id' => $this->user->id,
            'read_at' => null,
        ]);

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

        $response->assertOk();

        $this->assertDatabaseCount($this->table, 0);
    }
}
