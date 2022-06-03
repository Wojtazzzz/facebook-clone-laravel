<?php

namespace Database\Factories;

use App\Models\User;
use Mmo\Faker\PicsumProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition()
    {
        $this->faker->addProvider(new PicsumProvider($this->faker));

        $images = [
            [],
            [$this->faker->picsumUrl(850, 350)],
            [$this->faker->picsumUrl(850, 350), $this->faker->picsumUrl(850, 350), $this->faker->picsumUrl(850, 350)],
            [$this->faker->picsumUrl(850, 350), $this->faker->picsumUrl(850, 350), $this->faker->picsumUrl(850, 350), $this->faker->picsumUrl(850, 350)]
        ];

        $users = User::pluck('id');

        return [
            'content' => $this->faker->text(),
            'images' => $this->faker->randomElement($images),
            'author_id' => $this->faker->randomElement($users),
        ];
    }
}
