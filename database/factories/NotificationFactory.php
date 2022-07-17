<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FriendshipStatus;
use App\Models\Friendship;
use App\Models\Notification;
use App\Models\Poke;
use App\Models\User;
use App\Notifications\FriendshipRequestAccepted;
use App\Notifications\FriendshipRequestSent;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    private array $types = [
        FriendshipRequestAccepted::class,
        FriendshipRequestSent::class,
    ];

    public function definition(): array
    {
        $type = $this->getRandomType();

        $user = User::factory()->createOne();
        $friend = User::factory()->createOne();

        return [
            'id' => $this->faker->unique->uuid,
            'type' => $type,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [
                'friendId' => $friend->id,
                'message' => $this->getMessage($type),
                'link' => $this->getLink($type, $friend->id),
            ],
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Notification $notification) {
            $userId = $notification->notifiable_id;
            $friendId = $notification->data['friendId'];

            match ($notification->type) {
                FriendshipRequestAccepted::class => $this->generateConfirmedFriendship($userId, $friendId),
                FriendshipRequestSent::class => $this->generatePendingRequest($friendId, $userId),
                Poked::class => $this->generatePoke($friendId, $userId),
            };
        });
    }

    private function getRandomType(): string
    {
        return $this->faker->randomElement($this->types);
    }

    private function getMessage(string $type): string
    {
        return match ($type) {
            FriendshipRequestAccepted::class => 'Accepted your friendship invitation',
            FriendshipRequestSent::class => 'Sent you a friendship invitation',
            Poked::class => 'Poked you first time',
        };
    }

    private function getLink(string $type, int $friendId): string
    {
        return match ($type) {
            FriendshipRequestAccepted::class => "/profile/$friendId",
            FriendshipRequestSent::class => '/friends/invites',
            Poked::class => '/friends/pokes',
        };
    }

    private function generateConfirmedFriendship(int $userId, int $friendId): void
    {
        Friendship::factory()->createOne([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => FriendshipStatus::CONFIRMED,
        ]);
    }

    private function generatePendingRequest(int $userId, int $friendId): void
    {
        Friendship::factory()->createOne([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'status' => FriendshipStatus::PENDING,
        ]);
    }

    private function generatePoke(int $userId, int $friendId): void
    {
        Poke::factory()->createOne([
            'user_id' => $userId,
            'friend_id' => $friendId,
            'latest_initiator_id' => $userId,
        ]);
    }
}
