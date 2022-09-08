<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content ?? '',
            'images' => $this->images ?? [],
            'author' => new UserResource($this->author),
            'likes_count' => $this->likes_count,
            'comments_count' => $this->commenting ? $this->comments_count : 0,
            'is_liked' => (bool) $this->is_liked,
            'is_edited' => $this->when($this->created_at->notEqualTo($this->updated_at), true, false),
            'type' => [
                'is_saved' => $this->is_saved,
                'is_hidden' => $this->is_hidden,
                'is_own' => $this->author->id === $request->user()->id,
            ],
            'commenting' => $this->commenting,
            'created_at' => $this->created_at->diffAbsolute(),
        ];
    }
}
