<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        $author = new UserResource(User::findOrFail($this->author_id));

        return [
            'id' => $this->id,
            'content' => $this->content,
            'author' =>  $author,
            'resource_id' => $this->resource_id,
            'resource' => $this->resource->resource,
            'created_at' => $this->created_at->format('Y-m-d H:m'),
            'updated_at' => $this->updated_at->format('Y-m-d H:m'),
        ];
    }
}
