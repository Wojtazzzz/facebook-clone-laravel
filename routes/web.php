<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    $user = User::where('last_name', 'Witas')->first();
    $user->friends;
    dd($user);
});

require __DIR__.'/auth.php';
