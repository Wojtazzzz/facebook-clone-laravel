<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
        $date = now(); // it can be any date
        $weekAgo = now()->subWeek();
        $dayAgo = now()->subDay();

        $format = 'h:i';

        if ($date->isBefore($weekAgo)) {
            $format = 'j F Y \a\t h:i';
        } elseif ($date->isBefore($dayAgo)) {
            $format = 'l h:i';
        }

        return $date->dependentFormat();
    }
}
