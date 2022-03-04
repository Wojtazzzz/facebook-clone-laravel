<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    public function definition()
    {
        return [
            'text' => $this->faker->text(50),
            'sender_id' => 51,
            'receiver_id' => $this->faker->numberBetween(1, 50),
            'created_at' => $this->faker->date()
        ];
    }
}
