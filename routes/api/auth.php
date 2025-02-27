<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// routes/web.php
Route::get('/login', [AuthController::class, 'showError'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login'])->name('api.login');

Route::post('/auth/register', [AuthController::class, 'register'])->name('api.register');

Route::middleware('auth:sanctum')->get('/auth/getProfile', [AuthController::class, 'getProfile']);