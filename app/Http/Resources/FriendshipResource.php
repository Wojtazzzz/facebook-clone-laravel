<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FriendshipResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->user,
            'status' => $this->status,
            'inviter' => new UserResource($this->inviter),
            'invited' => new UserResource($this->invited),
        ];
    }
}
