<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'check.locked'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'getNotifications']);

    Route::post('/notification/read', [NotificationController::class, 'readNotification']);

    // Route::post('/notification/update', [NotificationController::class, 'updateCart']);

    // Route::post('/notification/delete', [NotificationController::class, 'deleteCart']);

    // Route::post('/notification/checkout', [NotificationController::class, 'addOrderToCart']);
});
