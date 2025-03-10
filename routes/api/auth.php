<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// routes/web.php
Route::get('/login', [AuthController::class, 'showError'])->name('login');

Route::post('/auth/register', [AuthController::class, 'register'])->name('api.register');

Route::post('/auth/login', [AuthController::class, 'login'])->name('api.login')->middleware('check.locked');;

Route::middleware('auth:sanctum')->get('/auth/getProfile', [AuthController::class, 'getProfile']);

/////////////////// Admin ///////////////////

Route::post('/auth/loginAdmin', [AuthController::class, 'loginAdmin'])->name('api.loginAdmin')->middleware('check.locked');

Route::middleware('auth:sanctum')->get('/auth/getProfileAdmin', [AuthController::class, 'getProfileAdmin']);