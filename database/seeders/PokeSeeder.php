<?php

namespace Database\Seeders;

use App\Models\Poke;
use Illuminate\Database\Seeder;

class PokeSeeder extends Seeder
{
    public function run()
    {
        Poke::factory(5)->create();
    }
}
