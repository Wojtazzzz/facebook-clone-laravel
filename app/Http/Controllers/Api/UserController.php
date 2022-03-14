<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserController extends Controller
{
    public function user(Request $request): JsonResource
    {
        return new UserResource($request->user());
    }
    
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
