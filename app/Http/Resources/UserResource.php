<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => "$this->first_name $this->last_name",
            'first_name' => $this->first_name,
            'profile_image' => $this->profile_image,
            'background_image' => $this->background_image,
        ];
    }
}
