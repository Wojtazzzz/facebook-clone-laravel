<?php

declare(strict_types=1);

namespace App\Http\Requests\Friendship\Invite;

use App\Enums\FriendshipStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([
                    FriendshipStatus::CONFIRMED->value,
                    FriendshipStatus::BLOCKED->value,
                ]),
            ]
        ];
    }
}
