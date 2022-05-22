<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PokeFactory extends Factory
{
    public function definition()
    {
        $usersCount = User::count();

        return [
            'initiator_id' => $this->faker->numberBetween(1, $usersCount),
            'poked_id' => $this->faker->numberBetween(1, $usersCount),
            'count' => $this->faker->numberBetween(1, 9999)
        ];
    }
}
