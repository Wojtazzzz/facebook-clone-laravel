<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PokeFactory extends Factory
{
    public function definition()
    {
        $rootUserId = User::where('last_name', 'Witas')->value('id');

        return [
            'initiator_id' => $this->faker->numberBetween(1, 50),
            'poked_id' => $rootUserId
        ];
    }
}
