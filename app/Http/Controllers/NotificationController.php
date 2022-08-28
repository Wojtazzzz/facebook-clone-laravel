<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Notification\UpdateRequest;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $notifications = $user->notifications;

        $pagination = $notifications->paginate(15);

        return response()->json([
            'data' => NotificationResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $user->notifications()
            ->whereNull('read_at')
            ->whereIn('id', $data['ids'])
            ->update([
                'read_at' => now(),
            ]);

        return response()->json(status: 200);
    }
}
