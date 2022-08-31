<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        $this->loadMissing('author');

        return [
            'id' => $this->id,
            'content' => $this->content,
            'author' => new UserResource($this->author),
            'resource_id' => $this->resource_id,
            'is_edited' => $this->when($this->created_at->notEqualTo($this->updated_at), true, false),
            'is_liked' => $this->is_liked,
            'likes_count' => $this->likes_count,
            'created_at' => $this->created_at->diffAbsolute(),
        ];
    }
}
