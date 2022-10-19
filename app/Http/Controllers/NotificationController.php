<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Notification\UpdateRequest;
use App\Http\Resources\NotificationResource;
use App\Services\PaginatedResponseFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pagination = $request->user()
            ->notifications()
            ->paginate(15);

        return PaginatedResponseFacade::response(NotificationResource::class, $pagination);
    }

    public function update(UpdateRequest $request): Response
    {
        $user = $request->user();
        $data = $request->validated();

        $user->notifications()
            ->whereNull('read_at')
            ->whereIn('id', $data['ids'])
            ->update([
                'read_at' => now(),
            ]);

        return response()->noContent();
    }

    public function checkUnread(Request $request): JsonResponse
    {
        $exist = $request->user()
            ->unreadNotifications()
            ->exists();

        return response()->json((bool) $exist);
    }
}
