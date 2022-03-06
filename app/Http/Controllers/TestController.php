<?php

namespace App\Http\Controllers;

use App\Models\User;

class TestController extends Controller
{
    public function __invoke()
    {
        $user = User::with('pokedBy')->findOrFail(51);
        
        $i = 0;
        foreach ($user->pokedBy as $pokedBy) {
            echo '<br/>';
            echo ++$i .'. '. $pokedBy->pokeInfo->count;
        }
    }
}