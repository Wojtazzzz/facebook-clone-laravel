<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('/user', function (Request $request) {
            $request->user()->friends;
        
            return $request->user();
        });

        Route::get('/requests', function (Request $request) {
            return response()->json([
                'requests' => $request->user()->requests
            ]);
        });
    });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $request->user()->friends;

    return $request->user();
});

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);