<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Http\Resources\MessengerNotificationResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Query\JoinClause;

class TestController extends Controller
{
    public function __invoke()
    {
        $user = User::firstWhere('last_name', 'Witas');
        
        $users = User::with([
            'sendedMessages' => fn($query) => $query
                ->where('messages.sender_id', $user->id)
                ->orWhere('messages.receiver_id', $user->id)
                ->select(['users.id', 'first_name', 'last_name', 'profile_image'])
                ->latest('messages.created_at'),

            'receivedMessages' => fn($query) => $query
                ->where('messages.sender_id', $user->id)
                ->orWhere('messages.receiver_id', $user->id)
                ->select(['users.id', 'first_name', 'last_name', 'profile_image'])
                ->latest('messages.created_at')
        ])
        ->whereNotIn('users.id', [$user->id])
        ->whereHas('sendedMessages', fn($query) => $query
            ->where('messages.receiver_id', $user->id)
            ->orWhere('messages.sender_id', $user->id)
        )
        ->orWhereHas('receivedMessages', fn ($query) => $query
            ->where('messages.receiver_id', $user->id)
            ->orWhere('messages.sender_id', $user->id)
        )

        ->join('messages', fn ($join) => $join
            ->on('users.id', 'messages.sender_id')
            ->orOn('users.id', 'messages.receiver_id')
            ->where('users.id', '!=', $user->id)
        )
        ->latest('messages.created_at')
        ->paginate(10, 
        [
            'messages.id',
            'messages.text',
            'messages.created_at',
            'users.id as friend_id',
            'users.first_name',
            'users.last_name',
            'users.profile_image',
        ]
        );

        $users->unique(['friend_id', 'id']);

        return($users);
        return response()->json(MessengerNotificationResource::collection($users));
    }
}