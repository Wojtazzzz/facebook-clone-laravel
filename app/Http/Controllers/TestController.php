<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Notifications\FriendshipRequestAccepted;
use Illuminate\Foundation\Testing\WithFaker;

class TestController extends Controller
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    public function __invoke(mixed $int)
    {
        $user = User::firstWhere('last_name', 'Witas');

        $user->notify(new FriendshipRequestAccepted());
    }
}
