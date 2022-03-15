<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageController extends Controller
{
    public function index(Request $request, int $receiverId): JsonResource
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
        
        return MessageResource::collection($messages);
    }

    public function store(StoreRequest $request)
    {
        Message::create($request->validated() + [
            'sender_id' => $request->user()->id
        ]);
    }

    public function messenger(Request $request)
    {
        $user = $request->user();

        $user->load('messages');
    
        return $user;
    }
}
