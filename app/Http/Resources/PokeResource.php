<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PokeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->initiator->id,
            'name' => $this->initiator->first_name .' '. $this->initiator->last_name,
            'first_name' => $this->initiator->first_name,
            'profile_image' => $this->initiator->profile_image,
            'poke_info' => [
                'id' => $this->id,
                'count' => $this->count,
                'updated_at' => $this->updated_at
            ]
        ];
    }
}