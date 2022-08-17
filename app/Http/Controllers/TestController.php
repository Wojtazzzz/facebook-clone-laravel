<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Carbon\Carbon;
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
        $from = Carbon::create('2022-08-18');
        $to = Carbon::now();


        return $from->diffWithConfig();
    }
}
