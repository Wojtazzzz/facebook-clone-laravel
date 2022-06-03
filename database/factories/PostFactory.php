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

        $users = User::pluck('id');

        return [
            'content' => $this->faker->text(),
            'images' => $this->getRandomImages(),
            'author_id' => $this->faker->randomElement($users),
        ];
    }

    private function getRandomImages(): array
    {
        return $this->faker->randomElement([
            [],
            [$this->faker->picsumStaticRandomUrl(850, 350)],
            [$this->faker->picsumStaticRandomUrl(850, 350), $this->faker->picsumStaticRandomUrl(850, 350)],
            [$this->faker->picsumStaticRandomUrl(850, 350), $this->faker->picsumStaticRandomUrl(850, 350), $this->faker->picsumStaticRandomUrl(850, 350)],
            [$this->faker->picsumStaticRandomUrl(850, 350), $this->faker->picsumStaticRandomUrl(850, 350), $this->faker->picsumStaticRandomUrl(850, 350), $this->faker->picsumStaticRandomUrl(850, 350)],
        ]);
    }
}
