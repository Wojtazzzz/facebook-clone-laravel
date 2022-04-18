<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-22 years');

        return [
            'text' => $this->faker->text(20),
            'sender_id' => $this->faker->numberBetween(1, User::count()),
            'receiver_id' => $this->faker->numberBetween(1, User::count()),
            'created_at' => $date,
            'updated_at' => $date
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Message $message) {
            while ($message->sender_id === $message->receiver_id) {
                $message->receiver_id = $this->faker->numberBetween(1, User::count());
            }
        });
    }
}
