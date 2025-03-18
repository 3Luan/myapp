<?php

use App\Http\Controllers\DiscountController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.locked'])->group(function () {
    Route::middleware('can:manageProduct,App\Models\Product')->group(function () {
        Route::get('/discounts', [DiscountController::class, 'getDiscounts']);

        Route::post('/discount/create', [DiscountController::class, 'create']);

        Route::post('/discount/{id}', [DiscountController::class, 'update']);

        Route::get('/discount/{id}', [DiscountController::class, 'getDiscountById']);

        Route::get('/discount/{id}/products', [DiscountController::class, 'getProductsInDiscount']);

        Route::post('/discount/{id}/products/add', [DiscountController::class, 'addProductsToDiscount']);

        Route::post('/discount/{id}/products/remove', [DiscountController::class, 'removeProductsFromDiscount']);
    });
});
