<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Poke\PokeRequest;
use App\Http\Resources\PokeResource;
use App\Http\Resources\UserResource;
use App\Models\Poke;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PokeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pokes = Poke::with('initiator')
            ->whereNot('latest_initiator_id', $request->user()->id)
            ->paginate(10, [
                'id',
                'user_id',
                'latest_initiator_id',
                'count',
                'updated_at',
            ]);

        return response()->json(PokeResource::collection($pokes));
    }

    public function poke(PokeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();
        $friend = User::findOrFail($data['friend_id']);

        $poke = Poke::poke($user->id, $data['friend_id'])->first('count');

        Poke::when(
            (bool) $poke,
            fn (Builder $query) => $query->update([
                    'latest_initiator_id' => $user->id,
                    'count' => $poke->count + 1,
                ]),
            fn (Builder $query) => $query->create([
                'user_id' => $user->id,
                'friend_id' => $friend->id,
                'latest_initiator_id' => $user->id,
            ])
        );

        return response()->json([
            'data' => new UserResource($friend),
            'message' => 'User poked successfully',
        ], 201);
    }
}
