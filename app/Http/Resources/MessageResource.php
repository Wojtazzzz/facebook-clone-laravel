<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class MessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'is_received' => $this->sender_id !== Auth::user()->id,
            'status' => $this->status,
            'read_at' => $this->read_at && $this->read_at->dependentFormat(),
            'created_at' => $this->created_at->dependentFormat(),
        ];
    }
}
