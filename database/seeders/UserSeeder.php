<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory(50)->create();

        $faker = Faker::create();

        User::create([
            'first_name' => 'Marcin',
            'last_name' => 'Witas',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
            'image' => $faker->imageUrl(64, 64)
        ]);
    }
}
