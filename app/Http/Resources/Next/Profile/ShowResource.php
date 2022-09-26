<?php

declare(strict_types=1);

namespace App\Http\Resources\Next\Profile;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'first_name' => $this->first_name,
                'profile_image' => $this->profile_image,
                'background_image' => $this->background_image,
                'created_at' => $this->created_at->format('F Y'),
                'born_at' => $this->born_at->format('j F Y'),
                'works_at' => $this->whenNotNull($this->works_at, ''),
                'went_to' => $this->whenNotNull($this->went_to, ''),
                'lives_in' => $this->whenNotNull($this->lives_in, ''),
                'from' => $this->whenNotNull($this->from, ''),
                'marital_status' => $this->whenNotNull($this->marital_status, ''),
            ],
        ];
    }
}
