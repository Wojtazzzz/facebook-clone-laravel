<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Poke\StoreRequest;
use App\Http\Requests\Poke\UpdateRequest;
use App\Http\Resources\PokeResource;
use App\Models\Poke;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PokeController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $pokes = Poke::with('initiator')
            ->where('poked_id', $request->user()->id)
            ->paginate(10, [
                'id',
                'initiator_id',
                'count',
                'updated_at'
            ]);

        return PokeResource::collection($pokes);
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        Poke::create([
            'initiator_id' => $request->user()->id,
            'poked_id' => $data['user_id'],
        ]);
    }

    public function update(UpdateRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        Poke::firstWhere([
            'initiator_id' => $data['user_id'],
            'poked_id' => $user->id
        ])->increment('count', 1, [
            'initiator_id' => $user->id,
            'poked_id' => $data['user_id']
        ]);
    }
}
