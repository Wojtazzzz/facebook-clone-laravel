<?php

namespace Database\Seeders;

use App\Models\Poke;
use Illuminate\Database\Seeder;

class PokeSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 20; $i++) { 
            Poke::create([
                'initiator_id' => $i,
                'poked_by_id' => $i,
                'poked_id' => 51,
                'count' => 1
            ]);
        }
    }
}
