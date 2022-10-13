<?php

declare(strict_types=1);

namespace App\Providers;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\ServiceProvider;

class FakerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Generator::class, function () {
            $faker = Factory::create();

            $faker->addProvider(new \Mmo\Faker\PicsumProvider($faker));
            $faker->addProvider(new \Bezhanov\Faker\Provider\Educator($faker));

            return $faker;
        });
    }

    public function boot(): void
    {
    }
}
