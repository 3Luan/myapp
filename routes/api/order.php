<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.locked'])->group(function () {
    Route::get('/orders', [OrderController::class, 'getOrders']);

    Route::post('/order/add', [OrderController::class, 'addOrder']);

    Route::post('/order/updateState', [OrderController::class, 'updateState']);
});
