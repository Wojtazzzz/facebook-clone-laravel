<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'author' => new UserResource($this->author),
            'resource_id' => $this->resource_id,
            'resource' => $this->resource->resource,
            'created_at' => $this->created_at->format('Y-m-d H:m'),
            'updated_at' => $this->updated_at->format('Y-m-d H:m'),
        ];
    }
}
