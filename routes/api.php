<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load(['friends' => function ($query) {
        $query->select([
            'users.id',
            'first_name',
            'last_name',
            'profile_image',
            'background_image'
        ]);
    }]);;
});