<?php

use App\Http\Controllers\TravelOrderController;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(JwtMiddleware::class)->group(function () {
    Route::apiResource('/travel-order', TravelOrderController::class)
        ->except(['update', 'destroy']);

    Route::patch('/travel-order/{id}/status', [TravelOrderController::class, 'updateStatus']);
});
