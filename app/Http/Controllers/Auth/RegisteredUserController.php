<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function store(): Response
    {
        $user = User::factory()->create();

        Auth::login($user);

        return response()->noContent();
    }
}
