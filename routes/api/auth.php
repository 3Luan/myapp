<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// routes/web.php
Route::get('/login', [AuthController::class, 'showError'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login'])->name('api.login');

// Route::post('/auth/refresh', [AuthController::class, 'refresh'])->name('api.refresh');

Route::middleware('auth:sanctum')->get('/auth/getProfile', [AuthController::class, 'getProfile']);
