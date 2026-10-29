<?php

use App\Modules\Payment\Controllers\RefundController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Refund Routes
|--------------------------------------------------------------------------
*/

// === USER ROUTES ===
Route::middleware('auth:sanctum')->group(function () {
    
    // درخواست استرداد برای سفارش
    Route::post('/v1/orders/{order}/refund-request', [RefundController::class, 'requestForOrder'])
        ->name('orders.refund_request');
    
    // لیست استرداد‌های کاربر
    Route::get('/v1/refunds', [RefundController::class, 'index'])
        ->name('refunds.index');
    
    // جزئیات استرداد
    Route::get('/v1/refunds/{refund}', [RefundController::class, 'show'])
        ->name('refunds.show');
    
    // لغو درخواست
    Route::post('/v1/refunds/{refund}/cancel', [RefundController::class, 'cancel'])
        ->name('refunds.cancel');
});

// === ADMIN ROUTES ===
Route::prefix('v1/admin/refunds')
    ->middleware(['auth:sanctum', 'log.admin'])
    ->group(function () {
        
        Route::get('/', [RefundController::class, 'adminIndex'])
            ->middleware('permission:refunds.view')
            ->name('admin.refunds.index');
        
        Route::get('/statistics', [RefundController::class, 'statistics'])
            ->middleware('permission:reports.view_financial')
            ->name('admin.refunds.statistics');
        
        Route::post('/{refund}/start-review', [RefundController::class, 'startReview'])
            ->middleware('permission:refunds.approve')
            ->name('admin.refunds.start_review');
        
        Route::post('/{refund}/approve', [RefundController::class, 'approve'])
            ->middleware('permission:refunds.approve')
            ->name('admin.refunds.approve');
        
        Route::post('/{refund}/reject', [RefundController::class, 'reject'])
            ->middleware('permission:refunds.approve')
            ->name('admin.refunds.reject');
        
        Route::post('/{refund}/process', [RefundController::class, 'process'])
            ->middleware('permission:refunds.process')
            ->name('admin.refunds.process');
        
        Route::post('/{refund}/complete', [RefundController::class, 'complete'])
            ->middleware('permission:refunds.process')
            ->name('admin.refunds.complete');
    });
