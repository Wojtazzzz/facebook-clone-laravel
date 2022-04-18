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
            'initiator_id' => $this->faker->numberBetween(1, floor(($usersCount - 1) / 2)),
            'poked_id' => $this->faker->numberBetween(floor(($usersCount + 1) / 2), $usersCount),
            'count' => $this->faker->numberBetween(1, 999)
        ];
    }
}
