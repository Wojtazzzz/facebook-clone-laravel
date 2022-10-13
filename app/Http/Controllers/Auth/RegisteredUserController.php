<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Faker\Generator;
use Illuminate\Container\Container;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    public function store(): Response
    {
        $faker = Container::getInstance()->make(Generator::class);

        $user = User::create([
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'email' => $faker->email,
            'password' => Hash::make($faker->iban),
            'profile_image' => $faker->picsumStaticRandomUrl(168, 168),
            'background_image' => $faker->picsumStaticRandomUrl(850, 350),
            'born_at' => now(),
        ]);

        Auth::login($user);

        return response()->noContent();
    }
}
