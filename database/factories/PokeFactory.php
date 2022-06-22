<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PokeFactory extends Factory
{
    public function definition(): array
    {
        $users = User::pluck('id');

        $userId = $this->faker->unique->randomElement($users);
        $friendId = $this->faker->unique->randomElement($users);
        $latestInitiatorId = $this->faker->randomElement([$userId, $friendId]);

        return [
            'user_id' => $userId,
            'friend_id' => $friendId,
            'latest_initiator_id' => $latestInitiatorId,
            'count' => $this->faker->numberBetween(1, 9999),
        ];
    }
}
