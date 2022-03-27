<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    public function definition()
    {
        $date = $this->faker->date;

        $images = $this->faker->randomElement([
            null,
            ['https://via.placeholder.com/850x350.png/0044cc?text=suscipit'],
            ['https://via.placeholder.com/850x350.png/0088dd?text=qui'],
            ['https://via.placeholder.com/850x350.png/0044cc?text=suscipit', 'https://via.placeholder.com/850x350.png/0044cc?text=suscipit', 'https://via.placeholder.com/850x350.png/0044cc?text=suscipit'],
            ['https://via.placeholder.com/850x350.png/0044cc?text=suscipit', 'https://via.placeholder.com/850x350.png/0044cc?text=suscipit', 'https://via.placeholder.com/850x350.png/0044cc?text=suscipit', 'https://via.placeholder.com/850x350.png/0044cc?text=suscipit', 'https://via.placeholder.com/850x350.png/0044cc?text=suscipit'],
        ]);

        return [
            'content' => $this->faker->text,
            'images' => $images,
            'author_id' => $this->faker->numberBetween(1, 30),
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
