<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PokeFactory extends Factory
{
    public function definition()
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

    public function user()
    {
        return $this->state(fn (array $attributes) => [
            'latest_initiator_id' => $attributes['user_id'],
        ]);
    }
}
