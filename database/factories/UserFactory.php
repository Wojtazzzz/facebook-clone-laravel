<?php

namespace Database\Factories;

use Mmo\Faker\PicsumProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use \Mmo\Faker\PicsumProvider;

class UserFactory extends Factory
{   
    public function definition()
<<<<<<< HEAD
    {       
        $this->faker->addProvider(new PicsumProvider($this->faker));
        
=======
    {
        $this->setupFaker();

>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique->email(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'profile_image' => $this->faker->picsumStaticRandomUrl(168, 168),
<<<<<<< HEAD
<<<<<<< HEAD
            'background_image' => $this->faker->picsumStaticRandomUrl(850, 350),
=======
            'background_image' => $this->faker->picsumStaticRandomUrl(850, 350)
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
=======
            'background_image' => $this->faker->picsumStaticRandomUrl(850, 350)
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
        ];
    }

    private function setupFaker(): void
    {
        $this->faker->addProvider(new PicsumProvider($this->faker));
    }
}
