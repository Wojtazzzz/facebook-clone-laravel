<?php

declare(strict_types=1);

namespace App\Providers;

use Bezhanov\Faker\Provider\Educator;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\ServiceProvider;
use Mmo\Faker\PicsumProvider;

class FakerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Generator::class, function () {
            $faker = Factory::create();

            $faker->addProvider(new PicsumProvider($faker));
            $faker->addProvider(new Educator($faker));

            return $faker;
        });
    }

    public function boot(): void
    {
    }
}
