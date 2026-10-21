<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - NextSafar
|--------------------------------------------------------------------------
| همه routes ماژول‌ها از اینجا بارگذاری می‌شوند.
| Laravel به صورت خودکار prefix 'api/' را اضافه می‌کند.
|
*/

// Health check route
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'version' => config('app.version', '1.0.0'),
    ]);
});

// Public routes (بدون نیاز به احراز هویت)
Route::prefix('auth')->group(function () {
    // Auth routes در AuthServiceProvider اضافه می‌شوند
});

// Protected routes (با نیاز به احراز هویت)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// ═══════════════════════════════════════════════════════════
// MODULE ROUTES
// ═══════════════════════════════════════════════════════════

// WordPress Webhooks
if (file_exists(base_path('app/Modules/WordPress/Routes/webhooks.php'))) {
    require base_path('app/Modules/WordPress/Routes/webhooks.php');
}

// Tour Module
if (file_exists(base_path('app/Modules/Tour/Routes/tours.php'))) {
    require base_path('app/Modules/Tour/Routes/tours.php');
}

// Lead Module
if (file_exists(base_path('app/Modules/Lead/Routes/leads.php'))) {
    require base_path('app/Modules/Lead/Routes/leads.php');
}

// Order Module
if (file_exists(base_path('app/Modules/Order/Routes/orders.php'))) {
    require base_path('app/Modules/Order/Routes/orders.php');
}

// Payment Module
if (file_exists(base_path('app/Modules/Payment/routes.php'))) {
    require base_path('app/Modules/Payment/routes.php');
}

// Ledger Module
if (file_exists(base_path('app/Modules/Ledger/Routes/ledger.php'))) {
    require base_path('app/Modules/Ledger/Routes/ledger.php');
}

// Refund Module
if (file_exists(base_path('app/Modules/Payment/Routes/refunds.php'))) {
    require base_path('app/Modules/Payment/Routes/refunds.php');
}

// Document Module
if (file_exists(base_path('app/Modules/Document/Routes/documents.php'))) {
    require base_path('app/Modules/Document/Routes/documents.php');
}

// Visa Module
if (file_exists(base_path('app/Modules/Visa/Routes/visa.php'))) {
    require base_path('app/Modules/Visa/Routes/visa.php');
}

// Notification Routes
require __DIR__.'/../app/Modules/Notification/Routes/notifications.php';

// ═══════════════════════════════════════════════════════
// AI Trip Planner Module
// ═══════════════════════════════════════════════════════
require base_path('app/Modules/TripPlanner/routes.php');


// ═══════════════════════════════════════════════════════
// AI Trip Planner Module
// ═══════════════════════════════════════════════════════
require base_path('app/Modules/TripPlanner/routes.php');

