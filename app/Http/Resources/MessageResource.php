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
            'isReceived' => $this->sender_id !== Auth::user()->id,
            'created_at' => $this->created_at->isoFormat('MMMM Do YYYY, h:mm:ss a'),
        ];
    }
}
