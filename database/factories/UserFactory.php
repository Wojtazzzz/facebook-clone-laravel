<?php

namespace Database\Factories;

use Mmo\Faker\PicsumProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{   
    public function definition()
    {       
        $this->faker->addProvider(new PicsumProvider($this->faker));
        
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique->email(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'profile_image' => $this->faker->picsumStaticRandomUrl(168, 168),
            'background_image' => $this->faker->picsumStaticRandomUrl(850, 350),
        ];
    }
}
