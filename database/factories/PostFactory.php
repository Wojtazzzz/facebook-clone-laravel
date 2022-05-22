<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Mmo\Faker\PicsumProvider;

class PostFactory extends Factory
{
    public function definition()
    {
        $this->setupFaker();

        $postsCount = User::count();
        $images = $this->getImages();
        $date = $this->faker->date;

        return [
            'content' => $this->faker->text,
            'images' => $images,
            'author_id' => $this->faker->numberBetween(1, $postsCount),
            'created_at' => $date,
            'updated_at' => $date
        ];
    }

    private function setupFaker(): void
    {
        $this->faker->addProvider(new PicsumProvider($this->faker));
    }

    private function getImages(): array
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
