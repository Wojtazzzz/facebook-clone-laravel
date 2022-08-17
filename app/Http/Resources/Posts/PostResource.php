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
            'is_liked' => (bool) $this->isLiked,
            'is_edited' => $this->when($this->created_at->notEqualTo($this->updated_at), true, false),
            'created_at' => $this->created_at->diffAbsolute(),
        ];
    }
}
