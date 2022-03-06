<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Poke\UpdateRequest;
use App\Models\Poke;
use App\Traits\CollectionPaginate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

class PokeController extends Controller
{
    use CollectionPaginate;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return response()->json([
            'paginator' => $this->paginate($user->pokedBy, 10)
        ]);
    }

    public function store(): JsonResponse
    {
        return response()->json([

        ]);
    }

    public function update(UpdateRequest $request): Response | ResponseFactory
    {
        $user = $request->user();
        $data = $request->validated();

        Poke::firstWhere([
            'poked_by_id' => $data['user_id'],
            'poked_id' => $user->id
        ])
        ->increment('count', 1, [
            'poked_by_id' => $user->id,
            'poked_id' => $data['user_id']
        ]);

        return response(status: 201);
    }
}
