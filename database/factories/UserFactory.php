<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MaritalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique->email,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'profile_image' => $this->faker->picsumStaticRandomUrl(168, 168),
            'background_image' => $this->faker->picsumStaticRandomUrl(850, 350),
            'works_at' => $this->faker->company,
            'went_to' => $this->faker->secondarySchool,
            'lives_in' => $this->generateRandomLocation(),
            'from' => $this->generateRandomLocation(),
            'marital_status' => $this->faker->randomElement(MaritalStatus::cases()),
            'born_at' => $this->faker->date(max: now()->subYears(50)),
        ];
    }

    private function generateRandomLocation(): string
    {
        return "{$this->faker->city}, {$this->faker->country}";
    }
}
