<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'images' => $this->images,
            'author' => new UserResource($this->author),
            'created_at' => $this->created_at->format('Y-m-d H-i-s'),
            'updated_at' => $this->updated_at->format('Y-m-d H-i-s'),
        ];
    }
}
