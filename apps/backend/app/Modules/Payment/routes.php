<?php

use App\Modules\Payment\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
*/

// =====================================================================
// PUBLIC ROUTES (بدون auth)
// =====================================================================

// Callback از درگاه (قانون ۱۴.۳.۲: اینجا فقط callback ثبت می‌شود)
Route::match(['get', 'post'], '/v1/payments/callback', [PaymentController::class, 'callback'])
    ->name('payment.callback');

// تأیید پرداخت (پس از بازگشت از درگاه)
Route::get('/v1/payments/verify/{code}', [PaymentController::class, 'verify'])
    ->name('payment.verify');

// =====================================================================
// USER ROUTES (Auth required)
// =====================================================================

Route::middleware('auth:sanctum')->group(function () {
    
    // شروع پرداخت برای Order جدید
    Route::post('/v1/orders/{order}/pay', [PaymentController::class, 'initiateForOrder'])
        ->name('orders.pay');
    
    // لیست پرداخت‌های کاربر
    Route::get('/v1/payments', [PaymentController::class, 'index'])
        ->name('payments.index');
    
    // جزئیات پرداخت
    Route::get('/v1/payments/{payment}', [PaymentController::class, 'show'])
        ->name('payments.show');
    
    // استعلام وضعیت (polling)
    Route::get('/v1/payments/{payment}/status', [PaymentController::class, 'status'])
        ->name('payments.status');
    
    // === LEGACY: Booking قدیمی (برای backward compatibility) ===
    Route::post('/v1/bookings/{bookingId}/pay', [PaymentController::class, 'pay'])
        ->name('payment.pay')
        ->where('bookingId', '[0-9]+');
});

// =====================================================================
// ADMIN ROUTES (Auth + Permission)
// =====================================================================

Route::prefix('v1/admin/payments')
    ->middleware(['auth:sanctum', 'log.admin'])
    ->group(function () {
        
        // لیست پرداخت‌ها
        Route::get('/', [PaymentController::class, 'adminIndex'])
            ->middleware('permission:payments.view')
            ->name('admin.payments.index');
        
        // آمار
        Route::get('/statistics', [PaymentController::class, 'statistics'])
            ->middleware('permission:reports.view_financial')
            ->name('admin.payments.statistics');
        
        // تطبیق دستی
        Route::post('/{payment}/reconcile', [PaymentController::class, 'reconcile'])
            ->middleware('permission:payments.reconcile')
            ->name('admin.payments.reconcile');
        
        // تلاش مجدد برای تأیید
        Route::post('/{payment}/retry-verification', [PaymentController::class, 'retryVerification'])
            ->middleware('permission:payments.verify')
            ->name('admin.payments.retry_verification');
    });
