<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class MessageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'images' => $this->whenNotNull($this->images, []),
            'is_received' => $this->sender_id !== Auth::user()->id,
            'status' => $this->status,
            'read_at' => $this->when(
                (bool) $this->read_at,
                fn () => $this->read_at->dependentFormat(),
                ''
            ),
            'created_at' => $this->created_at->dependentFormat(),
        ];
    }
}
