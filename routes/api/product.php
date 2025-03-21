<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.locked'])->group(function () {
    Route::middleware('can:manageProduct,App\Models\Product')->group(function () {

        Route::get('/allProducts', [ProductController::class, 'getAllProducts']);

        Route::post('/product/add', [ProductController::class, 'addProduct']);

        Route::post('/product/{id}', [ProductController::class, 'update']);

        Route::post('/product', [ProductController::class, 'deleteProduct']);

        Route::post('/import', [ProductController::class, 'importProduct']);
    });

    Route::get('/products', [ProductController::class, 'getProducts']);

    Route::get('/product/{id}', [ProductController::class, 'getProductDetails']);
});
