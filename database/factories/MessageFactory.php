<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition()
    {
<<<<<<< HEAD
        $users = User::pluck('id');

        return [
            'text' => $this->faker->text(),
            'sender_id' => $this->faker->randomElement($users),
            'receiver_id' => $this->faker->randomElement($users),
=======
        $date = $this->faker->dateTimeBetween('-2 years');
        $usersCount = User::count();

        return [
            'text' => $this->faker->text(25),
            'sender_id' => $this->faker->numberBetween(1, $usersCount),
            'receiver_id' => $this->faker->numberBetween(1, $usersCount),
            'created_at' => $date,
            'updated_at' => $date
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
        ];
    }
}
