<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition()
    {
        $images = $this->faker->randomElement([
            [],
            ['https://via.placeholder.com/850x350.png/0044cc?text=suscipit'],
            ['https://via.placeholder.com/850x350.png/0088dd?text=qui'],
            ['https://via.placeholder.com/850x350.png/0044cc?text=suscipit', 'https://via.placeholder.com/850x350.png/0044cc?text=suscipit', 'https://via.placeholder.com/850x350.png/0044cc?text=suscipit'],
            ['https://via.placeholder.com/850x350.png/0044cc?text=suscipit', 'https://via.placeholder.com/850x350.png/0044cc?text=suscipit', 'https://via.placeholder.com/850x350.png/0044cc?text=suscipit', 'https://via.placeholder.com/850x350.png/0044cc?text=suscipit', 'https://via.placeholder.com/850x350.png/0044cc?text=suscipit'],
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
