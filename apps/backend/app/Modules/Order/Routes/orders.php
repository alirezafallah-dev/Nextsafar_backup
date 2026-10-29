<?php

use App\Modules\Order\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Order Routes
|--------------------------------------------------------------------------
*/

// === PUBLIC ENDPOINTS (Auth required - کاربر لاگین‌کرده) ===
Route::prefix('v1/orders')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', [OrderController::class, 'myOrders'])
            ->name('orders.my');
        
        Route::get('/{order}', [OrderController::class, 'myShow'])
            ->name('orders.my.show');
        
        Route::post('/{order}/refund-request', [OrderController::class, 'requestRefund'])
            ->name('orders.refund_request');
    });

// === ADMIN ENDPOINTS (Auth + Permission) ===
Route::prefix('v1/admin/orders')
    ->middleware(['auth:sanctum', 'log.admin'])
    ->group(function () {
        
        // === CRUD اصلی ===
        Route::get('/', [OrderController::class, 'index'])
            ->middleware('permission:bookings.view')
            ->name('admin.orders.index');
        
        Route::get('/statistics', [OrderController::class, 'statistics'])
            ->middleware('permission:reports.view_sales')
            ->name('admin.orders.statistics');
        
        Route::post('/', [OrderController::class, 'store'])
            ->middleware('permission:bookings.create')
            ->name('admin.orders.store');
        
        Route::get('/{order}', [OrderController::class, 'show'])
            ->middleware('permission:bookings.view')
            ->name('admin.orders.show');
        
        Route::delete('/{order}', [OrderController::class, 'destroy'])
            ->middleware('permission:bookings.edit')
            ->name('admin.orders.destroy');
        
        // === تبدیل Lead ===
        Route::post('/convert-lead/{lead}', [OrderController::class, 'convertLead'])
            ->middleware('permission:leads.convert_to_booking')
            ->name('admin.orders.convert_lead');
        
        // === تغییر وضعیت ===
        Route::post('/{order}/status', [OrderController::class, 'changeStatus'])
            ->middleware('permission:bookings.change_status')
            ->name('admin.orders.change_status');
        
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])
            ->middleware('permission:bookings.cancel')
            ->name('admin.orders.cancel');
        
        // === مدیریت آیتم‌ها ===
        Route::post('/{order}/items', [OrderController::class, 'addItem'])
            ->middleware('permission:bookings.edit')
            ->name('admin.orders.items.store');
        
        Route::delete('/items/{item}', [OrderController::class, 'removeItem'])
            ->middleware('permission:bookings.edit')
            ->name('admin.orders.items.destroy');
        
        // === مدیریت مسافران ===
        Route::post('/{order}/passengers', [OrderController::class, 'addPassenger'])
            ->middleware('permission:bookings.edit')
            ->name('admin.orders.passengers.store');
        
        Route::post('/{order}/passengers/bulk', [OrderController::class, 'addPassengersBulk'])
            ->middleware('permission:bookings.edit')
            ->name('admin.orders.passengers.bulk');
        
        Route::delete('/passengers/{passenger}', [OrderController::class, 'removePassenger'])
            ->middleware('permission:bookings.edit')
            ->name('admin.orders.passengers.destroy');
        
        // === سایر عملیات ===
        Route::post('/{order}/recalculate', [OrderController::class, 'recalculate'])
            ->middleware('permission:bookings.edit')
            ->name('admin.orders.recalculate');
        
        Route::post('/{order}/assign', [OrderController::class, 'assign'])
            ->middleware('permission:bookings.edit')
            ->name('admin.orders.assign');
        
        Route::post('/{order}/initiate-payment', [OrderController::class, 'initiatePayment'])
            ->middleware('permission:bookings.change_status')
            ->name('admin.orders.initiate_payment');
    });
