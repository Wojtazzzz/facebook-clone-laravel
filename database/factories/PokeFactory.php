<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PokeFactory extends Factory
{
    public function definition(): array
    {
        $user = User::factory()->createOne();

        return [
            'user_id' => $user->id,
            'friend_id' => User::factory(),
            'latest_initiator_id' => $user->id,
            'count' => $this->faker->numberBetween(1, 9999),
        ];
    }
}
