<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.locked'])->group(function () {
    Route::get('/carts', [CartController::class, 'getCarts']);

    Route::post('/cart/add', [CartController::class, 'addCart']);

    Route::post('/cart/update', [CartController::class, 'updateCart']);

    Route::post('/cart/delete', [CartController::class, 'deleteCart']);

    Route::post('/cart/checkout', [CartController::class, 'addOrderToCart']);
});
