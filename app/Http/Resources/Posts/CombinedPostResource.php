<?php

declare(strict_types=1);

namespace App\Http\Resources\Posts;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CombinedPostResource extends JsonResource
{
    public function toArray($request): array
    {
        return $this->author->id === Auth::user()->id
            ? [$this->merge(new OwnPostResource($this))]
            : [$this->merge(new FriendPostResource($this))];
    }
}
