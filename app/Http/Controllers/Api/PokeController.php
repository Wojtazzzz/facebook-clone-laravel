<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Poke\StoreRequest;
use App\Http\Requests\Poke\UpdateRequest;
use App\Http\Resources\PokeResource;
use App\Http\Resources\UserResource;
use App\Models\Poke;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PokeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pokes = Poke::with('initiator')
            ->where('poked_id', $request->user()->id)
            ->paginate(10, [
                'id',
                'initiator_id',
                'count',
                'updated_at'
            ]);

        return response()->json(PokeResource::collection($pokes));
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $pokedUser = User::findOrFail($data['user_id']);

        Poke::create([
            'initiator_id' => $request->user()->id,
            'poked_id' => $pokedUser->id,
        ]);

        return response()->json([
            'data' => new UserResource($pokedUser),
            'message' => 'User poked successfully'
        ], 201);
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $pokedUser = User::findOrFail($data['user_id']);

        Poke::firstWhere([
            'initiator_id' => $pokedUser->id,
            'poked_id' => $user->id
        ])->increment('count', 1, [
            'initiator_id' => $user->id,
            'poked_id' => $pokedUser->id
        ]);

        return response()->json([
            'data' => new UserResource($pokedUser),
            'message' => 'User poked back successfully'
        ], 201);
    }
}
