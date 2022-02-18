<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'friends']);

require __DIR__.'/auth.php';
