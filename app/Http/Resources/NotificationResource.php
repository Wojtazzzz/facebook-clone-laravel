<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'data' => [
                'type' => $this->data['type'],
                'initiator' => new UserResource((object) $this->data['initiator'])
            ],
            'read_at' => $this->read_at,
            'created_at' => $this->created_at
        ];
    }
}