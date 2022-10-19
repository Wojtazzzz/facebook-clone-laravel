<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\BirthdayResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BirthdayController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $user = $request->user();

        $users = $user->friends->filter(fn ($value) => $value->born_at->dayOfYear === now()->dayOfYear());

        return BirthdayResource::collection($users);
    }
}
