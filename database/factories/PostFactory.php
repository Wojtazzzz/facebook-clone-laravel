<?php

namespace Database\Factories;

use App\Models\User;
use Mmo\Faker\PicsumProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Mmo\Faker\PicsumProvider;

class PostFactory extends Factory
{
    public function definition()
    {
<<<<<<< HEAD
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
=======
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
>>>>>>> 25181a0b59c051a99be7067ce7e0a4614e6be8a7
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
