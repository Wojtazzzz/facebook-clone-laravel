<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition()
    {
        $this->faker->addProvider(new \Mmo\Faker\PicsumProvider($this->faker));

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'profile_image' => $this->faker->picsumUrl(168, 168),
            'background_image' => $this->faker->picsumUrl(850, 350)
        ];
    }
}
