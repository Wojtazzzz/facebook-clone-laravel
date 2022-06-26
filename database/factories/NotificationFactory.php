<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Notifications\FriendshipInvitationAccepted;
use App\Notifications\FriendshipInvitationSent;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    private array $types = [
        FriendshipInvitationSent::class,
        FriendshipInvitationAccepted::class,
    ];

    public function definition(): array
    {
        $type = $this->getRandomType();

        return [
            'id' => $this->faker->unique->uuid(),
            'type' => $type,
            'notifiable_type' => User::class,
            'notifiable_id' => $this->faker->randomElement(User::pluck('id')),
            'data' => [
                'friendId' => $this->faker->randomElement(User::pluck('id')),
                'message' => $this->getMessage($type),
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
            FriendshipInvitationSent::class => 'Sent you a friendship invitation',
            FriendshipInvitationAccepted::class => 'Accepted your friendship invitation',
        };
    }
}
