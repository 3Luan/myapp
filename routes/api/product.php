<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/products', [ProductController::class, 'getProducts']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/product/add', [ProductController::class, 'addProduct']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/product/{id}', [ProductController::class, 'getProductDetails']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/product/{id}', [ProductController::class, 'update']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::delete('/product/{id}', [ProductController::class, 'delete']);
});