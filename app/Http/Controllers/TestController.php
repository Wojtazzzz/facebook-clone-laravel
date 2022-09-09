<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Bezhanov\Faker\Provider\Educator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Mmo\Faker\PicsumProvider;

class TestController extends Controller
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();

        $this->faker->addProvider(new PicsumProvider($this->faker));
        $this->faker->addProvider(new Educator($this->faker));
    }

    public function __invoke(Request $request)
    {
        $user = User::firstWhere('id', 1);

        dd($user->name);
    }
}
