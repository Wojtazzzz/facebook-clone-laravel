<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PokeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'friend' => new UserResource($this->initiator),
            'data' => [
                'id' => $this->id,
                'count' => $this->count,
                'updated_at' => $this->updated_at->diffAbsolute(),
            ],
        ];
    }
}
