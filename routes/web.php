<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'suggests']);

require __DIR__.'/auth.php';
