<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FriendshipStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FriendshipFactory extends Factory
{
    public function definition(): array
    {
        $statuses = [
            FriendshipStatus::CONFIRMED,
            FriendshipStatus::PENDING,
            FriendshipStatus::BLOCKED,
        ];

        return [
            'user_id' => User::factory(),
            'friend_id' => User::factory(),
            'status' => $this->faker->randomElement($statuses),
        ];
    }
}
