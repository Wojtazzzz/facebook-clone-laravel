<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TestController extends Controller
{
    public function __invoke()
    {
        $user = User::firstWhere('last_name', 'Witas');

        $friends = User::query()
            ->whereHas('invitedByFriends', fn(Builder $query) => $query
                    ->where('user_id', $user->id)
                    ->orWhere('friend_id', $user->id)
            )
            ->orWhereHas('invitedFriends', fn(Builder $query) => $query
                    ->where('user_id', $user->id)
                    ->orWhere('friend_id', $user->id)
            )
            ->inRandomOrder()
            ->paginate(10);

        return $friends;
    }
}
