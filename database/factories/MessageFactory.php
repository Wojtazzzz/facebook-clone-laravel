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
        $senderId = $this->faker->randomElement([51, 1]);

        return [
            'text' => $this->faker->text(50),
            'sender_id' => $senderId,
            'receiver_id' => ($senderId === 51) ? 1 : 51,
            'created_at' => $this->faker->date()
        ];
    }
}
