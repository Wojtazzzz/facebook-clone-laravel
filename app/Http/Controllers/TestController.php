<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\PaginatedResponseFacade;
use Bezhanov\Faker\Provider\Educator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Mmo\Faker\PicsumProvider;

class TestController extends Controller
{
    use WithFaker;

    public function __construct()
    {

    }

    public function __invoke(Request $request)
    {

    }
}
