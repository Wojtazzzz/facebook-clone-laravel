<?php

declare(strict_types=1);

namespace App\Http\Resources\Next\Profile;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowResource extends JsonResource
{
    public function toArray($request): array
    {
        $friends = [
            ...$this->invitedFriends,
            ...$this->invitedByFriends,
        ];

        return [
            'user' => [
                'id' => $this->id,
                'name' => "$this->first_name $this->last_name",
                'first_name' => $this->first_name,
                'profile_image' => $this->profile_image,
                'background_image' => $this->background_image,
                'created_at' => $this->created_at->format('F Y'),
                'works_at' => $this->works_at,
                'went_to' => $this->went_to,
                'lives_in' => $this->lives_in,
                'from' => $this->from,
                'marital_status' => $this->marital_status,
            ],
            'friends' => [
                'amount' => count($friends),
                'list' => UserResource::collection($friends),
            ],
        ];
    }
}
