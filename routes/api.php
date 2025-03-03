<?php

use App\Http\Controllers\TravelOrderController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::apiResource('/travel-order', TravelOrderController::class)
    ->middleware(JwtMiddleware::class);
