<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class MessageController extends Controller
{
    public function index(Request $request, int $receiverId): JsonResponse
    {
        $sendedMessages = Message::where([
            ['sender_id', $request->user()->id],
            ['receiver_id', $receiverId]
        ])->get(['id', 'text', 'sender_id', 'receiver_id', 'created_at']);

        $receivedMessages = Message::where([
            ['sender_id', $receiverId],
            ['receiver_id', $request->user()->id]
        ])->get(['id', 'text', 'sender_id', 'receiver_id', 'created_at']);

        $messages = $sendedMessages->merge($receivedMessages);
        
        return response()->json([
            'paginator' => $this->paginate($messages->sortByDesc('created_at')->values())
        ]);
    }

    private function paginate($items, $perPage = 15, $page = null, $options = []): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
