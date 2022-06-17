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
        $users = User::pluck('id');
        $status = [FriendshipStatus::CONFIRMED, FriendshipStatus::PENDING, FriendshipStatus::BLOCKED];

        return [
            'user_id' => $this->faker->unique->randomElement($users),
            'friend_id' => $this->faker->unique->randomElement($users),
            'status' => $this->faker->randomElement($status),
        ];
    }
}
