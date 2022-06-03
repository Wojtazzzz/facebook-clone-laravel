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
<<<<<<< HEAD
            'initiator_id' => $this->faker->unique->randomElement($users),
            'poked_id' => $this->faker->unique->randomElement($users),
            'count' => $this->faker->numberBetween(1, 9999),
=======
            'initiator_id' => $this->faker->numberBetween(1, $usersCount),
            'poked_id' => $this->faker->numberBetween(1, $usersCount),
            'count' => $this->faker->numberBetween(1, 9999)
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
        ];
    }
}
