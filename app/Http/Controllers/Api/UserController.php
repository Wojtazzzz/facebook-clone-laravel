<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'users' => User::get()
        ]);
    }

    public function show(User $user): JsonResponse
    {
        $user->friends;

        return response()->json([
            'user' => $user 
        ]);
    }
}
