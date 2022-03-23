<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessengerNotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'friend' => [
                'id' => $this->friend_id,
                'name' => "$this->first_name $this->last_name",
                'profile_image' => $this->profile_image
            ],
            'created_at' => $this->created_at
        ];
    }
}
