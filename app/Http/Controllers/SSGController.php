<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Next\Profile\IndexResource;
use App\Http\Resources\Next\Profile\ShowResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class SSGController extends Controller
{
    public function index(): JsonResource
    {
        $users = User::get('id');

        return IndexResource::collection($users);
    }

    public function show(User $user): JsonResource
    {
        return new ShowResource($user);
    }
}
