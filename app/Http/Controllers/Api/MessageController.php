<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreRequest;
use App\Models\Message;
use App\Traits\CollectionPaginate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

class MessageController extends Controller
{
    use CollectionPaginate;

    private array $messengerColumns = ['users.id', 'first_name', 'last_name', 'profile_image', 'background_image', 'messages.created_at', 'messages.text as message']; 

    public function index(Request $request, int $receiverId): JsonResponse
    {
        $messages = Message::where([
            ['sender_id', $request->user()->id],
            ['receiver_id', $receiverId]
        ])
        ->orWhere([
            ['sender_id', $receiverId],
            ['receiver_id', $request->user()->id],
        ])
        ->latest()
        ->paginate(15, ['id', 'text', 'sender_id', 'receiver_id', 'created_at']);
        
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

    public function messenger(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->load([
            'messages' => function ($query) use ($user) {
                $query
                    ->with(['messages' => function ($subQuery) use ($user) {
                        $subQuery
                            ->where('receiver_id', $user->id)
                            ->latest('messages.created_at')
                            ->limit(1)
                            ->select($this->messengerColumns);
                    }])
                    ->latest('messages.created_at')
                    ->select($this->messengerColumns);
            }
        ]);

        $messages = $user->messages->unique()->map(function ($item) {
            $userMessage = $item;
            $friendMessage = $item->messages[0] ?? null;

            if (!$friendMessage || $userMessage->created_at > $friendMessage->created_at) {
                unset($userMessage->messages);

                $model = $userMessage;
            }
            
            $model = $model ?? $friendMessage;

            return $model->makeVisible('created_at');
        });

        return response()->json([
            'paginator' => $this->paginate($messages, 10)
        ]);
    }
}
