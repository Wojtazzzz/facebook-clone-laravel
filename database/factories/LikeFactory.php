<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 30),
            'post_id' => $this->faker->numberBetween(1, 50),
            'created_at' => $this->faker->date()
        ];
    }
}
