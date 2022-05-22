<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use \Mmo\Faker\PicsumProvider;

class UserFactory extends Factory
{   
    public function definition()
    {
        $this->setupFaker();

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'profile_image' => $this->faker->picsumStaticRandomUrl(168, 168),
            'background_image' => $this->faker->picsumStaticRandomUrl(850, 350)
        ];
    }

    private function setupFaker(): void
    {
        $this->faker->addProvider(new PicsumProvider($this->faker));
    }
}
