<?php

namespace App\Http\Resources\Posts;

use App\Http\Resources\UserResource;
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
            'likes_count' => $this->likes_count,
            'comments_count' => $this->comments_count,
            'isLiked' => (bool) $this->isLiked,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
