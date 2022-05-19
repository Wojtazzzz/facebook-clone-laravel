<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition()
    {
        $this->faker->addProvider(new \Mmo\Faker\PicsumProvider($this->faker));

        $images = $this->faker->randomElement([
            [],
            [$this->faker->picsumUrl(850, 350)],
            [$this->faker->picsumUrl(850, 350)],
            [$this->faker->picsumUrl(850, 350), $this->faker->picsumUrl(850, 350), $this->faker->picsumUrl(850, 350)],
            [$this->faker->picsumUrl(850, 350), $this->faker->picsumUrl(850, 350), $this->faker->picsumUrl(850, 350), $this->faker->picsumUrl(850, 350), $this->faker->picsumUrl(850, 350)],
        ]);

        $date = $this->faker->date;

        return [
            'content' => $this->faker->text,
            'images' => $images,
            'author_id' => $this->faker->numberBetween(1, User::count()),
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
