<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function user(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'users' => User::get('id'),
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(new UserResource($user));
    }
}
