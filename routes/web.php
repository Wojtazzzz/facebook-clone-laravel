<?php

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/', TestController::class);

require __DIR__.'/auth.php';

