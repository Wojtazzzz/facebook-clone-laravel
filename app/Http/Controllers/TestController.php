<?php

namespace App\Http\Controllers;

use App\Models\Poke;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Foundation\Testing\WithFaker;

class TestController extends Controller
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();    
    }

    public function __invoke()
    {
        $user = User::firstWhere('last_name', 'Witas');

        // for ($i = 1; $i <= Poke::count(); $i++) { 
        //     $poke = Poke::findOrFail($i);

        //     $searched = Poke::where([
        //         ['initiator_id', $poke->initiator_id],
        //         ['poked_id', $poke->poked_id]
        //     ])->orWhere([
        //         ['initiator_id', $poke->poked_id],
        //         ['poked_id', $poke->initiator_id]
        //     ])->get();

        //     if (count($searched) > 1) {
        //         dump($searched);
        //         return 'DUPA';
        //     } 
        // }

        dd($this->faker->dateTimeBetween('-22 years'));
    }
}
