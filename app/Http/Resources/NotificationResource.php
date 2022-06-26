<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        $friend = User::findOrFail($this->data['friendId']);

        return [
            'id' => $this->id,
            'message' => $this->data['message'],
            'friend' => new UserResource($friend),
            'link' => $this->data['link'],
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
        ];
    }
}
