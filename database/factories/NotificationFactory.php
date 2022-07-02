<?php

declare(strict_types=1);

namespace Database\Factories;

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
        $uuid = $this->faker->unique->uuid();

        $friendId = $this->faker->randomElement(User::pluck('id'));

        return [
            'id' => $uuid,
            'type' => $type,
            'notifiable_type' => User::class,
            'notifiable_id' => $this->faker->randomElement(User::pluck('id')),
            'data' => [
                'friendId' => $friendId,
                'message' => $this->getMessage($type),
                'link' => $this->getLink($type, $friendId),
            ],
        ];
    }

    private function getRandomType(): string
    {
        return $this->faker->randomElement($this->types);
    }

    private function getMessage(string $type): string
    {
        return match ($type) {
            FriendshipRequestAccepted::class => 'Sent you a friendship invitation',
            FriendshipRequestSent::class => 'Accepted your friendship invitation',
            // Poked::class,
        };
    }

    private function getLink(string $type, int $friendId): string
    {
        return match ($type) {
            FriendshipRequestAccepted::class => "/profile/$friendId",
            FriendshipRequestSent::class => '/friends/invites',
            // Poked::class => '/friends/pokes',
        };
    }
}
