<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\MessengerNotificationResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request, int $receiverId): JsonResponse
    {
        $messages = Message::where([
            ['sender_id', $request->user()->id],
            ['receiver_id', $receiverId]
        ])->orWhere([
            ['sender_id', $receiverId],
            ['receiver_id', $request->user()->id],
        ])
        ->latest()
        ->paginate(15, [
            'id',
            'text',
            'sender_id',
            'created_at'
        ]);
        
        return response()->json(MessageResource::collection($messages));
    }
    
    public function store(StoreRequest $request): JsonResponse
    {
        $message = Message::create($request->validated() + [
            'sender_id' => $request->user()->id
        ]);
        
        return response()->json(new MessageResource($message), 201);
    }

    public function messenger(Request $request): JsonResponse
    {
        $user = $request->user();

        $users = User::with(['sendedMessages', 'receivedMessages'])
        // ->with([
        //     'sendedMessages' => fn($query) => $query
        //         ->where('messages.sender_id', $user->id)
        //         ->orWhere('messages.receiver_id', $user->id)
        //         ->select(['users.id', 'first_name', 'last_name', 'profile_image'])
        //         ->latest('messages.created_at'),

        //     'receivedMessages' => fn($query) => $query
        //         ->where('messages.sender_id', $user->id)
        //         ->orWhere('messages.receiver_id', $user->id)
        //         ->select(['users.id', 'first_name', 'last_name', 'profile_image'])
        //         ->latest('messages.created_at')
        // ])
        ->whereNotId(fn ($query) => $query->where('users.id', $user->id))
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
        )
        ->latest('messages.created_at')
        ->paginate(10, [
            'messages.id',
            'messages.text',
            'messages.created_at',
            'users.id as friend_id',
            'users.first_name',
            'users.last_name',
            'users.profile_image',
        ]);

        $users->unique('friend_id');
    
        return response()->json(MessengerNotificationResource::collection($users));
    }
}