<?php

declare(strict_types=1);

namespace App\Http\Resources\Posts;

use App\Enums\PostType;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnPostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => PostType::OWN,
            $this->merge(new PostResource($this)),
        ];
    }
}
