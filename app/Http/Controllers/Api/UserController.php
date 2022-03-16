<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            'users' => User::get()
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($user)
        ]);
    }
}
