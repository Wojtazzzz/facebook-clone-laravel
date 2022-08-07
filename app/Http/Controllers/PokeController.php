<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Poke\PokeRequest;
use App\Http\Resources\PokeResource;
use App\Models\Poke;
use App\Models\User;
use App\Notifications\Poked;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PokeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $pagination = Poke::with('initiator')
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('friend_id', $userId);
            })
            ->whereNot('latest_initiator_id', $userId)
            ->paginate(10, [
                'id',
                'user_id',
                'latest_initiator_id',
                'count',
                'updated_at',
            ]);

        return response()->json([
            'data' => PokeResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }

    public function poke(PokeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $friend = User::findOrFail($data['friend_id']);

        $pokeCount = Poke::poke($user->id, $data['friend_id'])->value('count');

        Poke::when(
            (bool) $pokeCount,
            fn (Builder $query) => $query->poke($user->id, $data['friend_id'])->update([
                'latest_initiator_id' => $user->id,
                'count' => $pokeCount + 1,
            ]),
            fn (Builder $query) => $query->create([
                'user_id' => $user->id,
                'friend_id' => $friend->id,
                'latest_initiator_id' => $user->id,
            ])
        );

        $poke = Poke::poke($user->id, $data['friend_id'])->firstOrFail();

        $friend->notify(new Poked($user->id, $poke->count));

        return response()->json([
            'message' => 'User poked successfully',
        ], 201);
    }
}
