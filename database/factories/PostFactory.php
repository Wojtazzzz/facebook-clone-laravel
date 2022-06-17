<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Mmo\Faker\PicsumProvider;

class PostFactory extends Factory
{
    public function definition(): array
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
