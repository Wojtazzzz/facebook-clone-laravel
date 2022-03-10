<?php

namespace App\Http\Controllers;

use App\Models\User;

class TestController extends Controller
{
    public function __invoke()
    {
        $user = User::findOrFail(21);

        $notifications = $user->notifications;

        return $notifications;
    }
}