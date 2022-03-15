<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FriendshipFactory extends Factory
{
    public function definition()
    {
        $rootUserId = User::where('last_name', 'Witas')->value('id');

        return [
            'user_id' => $rootUserId,
            'friend_id' => $this->faker->unique->numberBetween(1, 30),
            'status' => $this->faker->randomElement(['CONFIRMED', 'PENDING', 'BLOCKED'])
        ];
    }
}
