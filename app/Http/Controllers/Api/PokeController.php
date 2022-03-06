<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Poke\UpdateRequest;
use App\Models\Poke;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\ResponseFactory;

class PokeController extends Controller
{
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

    private function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
