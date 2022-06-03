<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PokeFactory extends Factory
{
    public function definition()
    {
        $users = User::pluck('id');
        
        return [
            'initiator_id' => $this->faker->unique->randomElement($users),
            'poked_id' => $this->faker->unique->randomElement($users),
            'count' => $this->faker->numberBetween(1, 9999),
        ];
    }
}
