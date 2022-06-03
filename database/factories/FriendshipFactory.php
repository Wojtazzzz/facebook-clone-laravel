<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FriendshipFactory extends Factory
{
    public function definition()
    {
        $users = User::pluck('id');
        $status = ['PENDING', 'CONFIRMED', 'BLOCKED'];

        return [
            'user_id' => $this->faker->unique->randomElement($users),
            'friend_id' => $this->faker->unique->randomElement($users),
            'status' => $this->faker->randomElement($status),
        ];
    }
}
