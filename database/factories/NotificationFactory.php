<?php

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

    public function definition()
    {
        $type = $this->getRandomType();

        return [
            'id' => $this->faker->unique->uuid(),
            'type' => $type,
            'notifiable_type' => User::class,
            'notifiable_id' => $this->faker->randomElement(User::pluck('id')),
        ];
    }

    private function getRandomType(): string
    {
        return $this->faker->randomElement($this->types);
    }
}
