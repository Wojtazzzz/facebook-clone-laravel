<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
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
        
        return response()->json(New MessageResource($message), 201);
    }

    public function messenger(): JsonResponse
    {
        $messages = [];
    
        return response()->json(MessageResource::collection($messages));
    }
}
