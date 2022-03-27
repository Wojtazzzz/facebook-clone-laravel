<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition()
    {
        $date = $this->faker->date;

        return [
            'content' => $this->faker->text,
            'author_id' => $this->faker->numberBetween(1, 30),
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
