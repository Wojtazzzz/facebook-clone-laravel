<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MessageStatus;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // $user === $friend
    public function index(Request $request, User $user): JsonResponse
    {
        $pagination = Message::query()
            ->conversation($request->user()->id, $user->id)
            ->latest()
            ->paginate(15, [
                'id',
                'content',
                'sender_id',
                'status',
                'read_at',
                'created_at',
            ]);

        return response()->json([
            'data' => MessageResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->sentMessages()->create($request->validated() + [
            'status' => MessageStatus::DELIVERED,
        ]);

        return response()->json(status: 201);
    }

    public function messenger(Request $request): JsonResponse
    {
        $user = $request->user();

        $pagination = $user->friends->paginate(15);

        return response()->json([
            'data' => UserResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }
}
