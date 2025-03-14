<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

// Broadcast::routes(['middleware' => ['auth:sanctum']]);require base_path('routes/api/user.php');
require base_path('routes/api/auth.php');
require base_path('routes/api/role.php');
require base_path('routes/api/product.php');
require base_path('routes/api/order.php');
require base_path('routes/api/user.php');
require base_path('routes/api/cart.php');
require base_path('routes/api/notification.php');
