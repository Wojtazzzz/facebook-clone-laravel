<?php

declare(strict_types=1);

namespace App\Http\Resources\SearchHits;

use Illuminate\Http\Resources\Json\JsonResource;

class UserHitResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => "{$this->first_name} $this->last_name",
            'profile_image' => $this->profile_image,
        ];
    }
}
