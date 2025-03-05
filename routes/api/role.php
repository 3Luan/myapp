<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.locked'])->group(function () {
    Route::middleware('can:manageRole,App\Models\Role')->group(function () {
        Route::get('/roles', [RoleController::class, 'getRoles']);
        Route::post('/role/add', [RoleController::class, 'addRole']);
    });
});
