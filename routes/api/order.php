<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.locked'])->group(function () {

    Route::middleware('can:manageProduct,App\Models\Product')->group(function () {
        Route::get('/orders', [OrderController::class, 'getOrders']);
    });

    Route::get('/ordersByUser', [OrderController::class, 'getOrdersByUser']);

    Route::post('/order/add', [OrderController::class, 'addOrder']);

    Route::post('/order/updateState', [OrderController::class, 'updateState']);
});
