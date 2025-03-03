<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/users', [UserController::class, 'getUsers']);
    Route::get('/user/{id}', [UserController::class, 'show']);
});
