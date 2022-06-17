<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Poke;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class PokeInitiator implements Rule
{
    public function passes($attribute, $value): bool
    {
        $userId = Auth::user()->id;
        $poke = Poke::poke($userId, $value)->first();

        return Poke::when((bool) $poke, fn () => $poke->latest_initiator_id === $value, fn () => true);
    }

    public function message(): string
    {
        return 'Cannot poke friend two times in a row.';
    }
}
