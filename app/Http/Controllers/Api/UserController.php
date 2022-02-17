<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function user(Request $request): User
    {
        $request->user()->friends;
        
        return $request->user();
    }

    public function invites(Request $request): JsonResponse
    {
        return response()->json([
            'invites' => $request->user()->invites
        ]);
    }

    public function suggests(Request $request): JsonResponse
    {
        $user = $request->user();

        $suggests = User::whereNotIn('id', [$user->id, ...$user->friends->pluck('id')])
            ->paginate(10, [
                'id',
                'first_name', 
                'last_name', 
                'profile_image'
            ]);

        return response()->json([
            'paginator' => $suggests
        ]);
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
