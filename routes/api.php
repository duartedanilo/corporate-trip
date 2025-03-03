<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelOrderController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('jwt')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('/travel-order', TravelOrderController::class)
        ->except(['update', 'destroy']);
    Route::patch('/travel-order/{id}/status', [TravelOrderController::class, 'updateStatus'])
        ->middleware('admin');
});
