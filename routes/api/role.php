<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/roles', [RoleController::class, 'getRoles']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/role/add', [RoleController::class, 'addRole']);
});