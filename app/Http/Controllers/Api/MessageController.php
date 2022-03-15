<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

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
            'receiver_id', 
            'created_at'
        ]);
        
        return response()->json([
            'paginator' => $messages
        ]);
    }

    public function store(StoreRequest $request): Response | ResponseFactory
    {
        Message::create($request->validated() + [
            'sender_id' => $request->user()->id
        ]);

        return response(status: 201);
    }

    public function messenger(Request $request)
    {
        $user = $request->user();

        $user->load('messages');
    
        return $user;
    }
}
