<?php

declare(strict_types=1);

namespace App\Http\Resources\Posts;

use App\Enums\PostType;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendPostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'type' => PostType::FRIEND,
            $this->merge(new PostResource($this)),
        ];
    }
}
