<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Next\Profile\IndexResource;
use App\Http\Resources\Next\Profile\ShowResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class NextController extends Controller
{
    public function profile(User $user): JsonResponse
    {
        return response()->json(new ShowResource($user));
    }

    public function profiles(): JsonResponse
    {
        $users = User::get('id');

        return response()->json(IndexResource::collection($users));
    }
}
