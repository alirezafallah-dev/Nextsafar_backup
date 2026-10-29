<?php

use App\Modules\Notification\Controllers\UserNotificationController;
use App\Modules\Notification\Controllers\AdminNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Notification Routes
|--------------------------------------------------------------------------
*/

// User Routes
Route::prefix('v1/notifications')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', [UserNotificationController::class, 'index'])
            ->name('notifications.index');
        
        Route::get('/unread-count', [UserNotificationController::class, 'unreadCount'])
            ->name('notifications.unread-count');
        
        Route::post('/{id}/mark-read', [UserNotificationController::class, 'markAsRead'])
            ->name('notifications.mark-read')
            ->where('id', '[0-9]+');
        
        Route::post('/mark-all-read', [UserNotificationController::class, 'markAllAsRead'])
            ->name('notifications.mark-all-read');
        
        Route::delete('/{id}', [UserNotificationController::class, 'destroy'])
            ->name('notifications.destroy')
            ->where('id', '[0-9]+');
    });

// Admin Routes
Route::prefix('v1/admin/notifications')
    ->middleware(['auth:sanctum', 'permission:notifications.manage'])
    ->group(function () {
        Route::get('/', [AdminNotificationController::class, 'index'])
            ->name('admin.notifications.index');
        
        Route::get('/statistics', [AdminNotificationController::class, 'statistics'])
            ->name('admin.notifications.statistics');
        
        Route::post('/send', [AdminNotificationController::class, 'send'])
            ->name('admin.notifications.send');
        
        Route::post('/{id}/retry', [AdminNotificationController::class, 'retry'])
            ->name('admin.notifications.retry')
            ->where('id', '[0-9]+');
        
        Route::post('/{id}/cancel', [AdminNotificationController::class, 'cancel'])
            ->name('admin.notifications.cancel')
            ->where('id', '[0-9]+');
        
        Route::get('/templates', [AdminNotificationController::class, 'templates'])
            ->name('admin.notifications.templates');
        
        Route::put('/templates/{id}', [AdminNotificationController::class, 'updateTemplate'])
            ->name('admin.notifications.templates.update')
            ->where('id', '[0-9]+');
    });
