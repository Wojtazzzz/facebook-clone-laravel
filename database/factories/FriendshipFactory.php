<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FriendshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user_id = User::where('last_name', 'Witas')->value('id');

        return [
            'first_user' => $user_id,
            'second_user' => $this->faker->unique->numberBetween(1, 20),
            'acted_user' => $user_id,
            'status' => 'confirmed'
        ];
    }
}
