<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FriendshipFactory extends Factory
{
    public function definition()
    {
        $user_id = User::findOrFail(51)->value('id');

        return [
            'user_id' => $user_id,
            'friend_id' => $this->faker->unique->numberBetween(1, 20),
            'status' => 'CONFIRMED'
        ];
    }
}
