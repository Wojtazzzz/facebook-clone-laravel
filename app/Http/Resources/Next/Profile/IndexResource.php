<?php

declare(strict_types=1);

namespace App\Http\Resources\Next\Profile;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
