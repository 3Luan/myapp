<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.locked'])->group(function () {
    Route::middleware('can:manageUser,App\Models\User')->group(function () {
        Route::get('/users', [UserController::class, 'getUsers']);
        Route::get('/user/{id}', [UserController::class, 'getUserById']);
        Route::post('/user/{id}', [UserController::class, 'update']);
    });
});
