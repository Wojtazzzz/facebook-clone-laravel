<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\BirthdayResource;
use Illuminate\Http\Request;

class BirthdayController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $users = $user->friends->filter(fn ($value) => $value->born_at->dayOfYear === now()->dayOfYear());

        return response()->json(BirthdayResource::collection($users));
    }
}
