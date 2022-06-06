<?php

namespace App\Rules;

use App\Models\Friendship;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RequestReceived implements Rule
{
    private string $status;

    public function __construct(string $status = 'PENDING')
    {
        $this->status = $status;
    }

    public function passes($attribute, $value)
    {
        return Friendship::where([
            'user_id' => $value,
            'friend_id' => Auth::user()->id,
        ])
        ->where('status', $this->status)
        ->exists();
    }

    public function message()
    {
        return 'This Friendship instance not exists.';
    }
}
