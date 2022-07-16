<?php

declare(strict_types=1);

namespace App\Http\Resources\Next\Profile;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowResource extends JsonResource
{
    public function toArray($request): array
    {
        $friends = [
            ...$this->invitedFriends,
            ...$this->invitedByFriends,
        ];

        return [
            'user' => new UserResource($this),
            'friends' => [
                'amount' => count($friends),
                'list' => $friends,
            ],
        ];
    }
}
