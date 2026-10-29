<?php

use App\Modules\Tour\Controllers\TourController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tour Routes
|--------------------------------------------------------------------------
*/

// === Public Routes (no auth) ===
Route::prefix('v1/tours')->group(function () {
    Route::get('/', [TourController::class, 'index'])->name('tours.index');
    Route::get('/featured', [TourController::class, 'featured'])->name('tours.featured');
    Route::get('/{slug}', [TourController::class, 'show'])->name('tours.show');
    Route::get('/{tour}/dates', [TourController::class, 'dates'])->name('tours.dates');
});

// === Admin Routes (auth + permissions) ===
Route::prefix('v1/admin/tours')
    ->middleware(['auth:sanctum', 'log.admin'])
    ->group(function () {
        
        Route::post('/', [TourController::class, 'store'])
            ->middleware('permission:tours.create')
            ->name('admin.tours.store');
        
        Route::get('/statistics', [TourController::class, 'statistics'])
            ->middleware('permission:reports.view_dashboard')
            ->name('admin.tours.statistics');
        
        Route::put('/{tour}', [TourController::class, 'update'])
            ->middleware('permission:tours.edit')
            ->name('admin.tours.update');
        
        Route::delete('/{tour}', [TourController::class, 'destroy'])
            ->middleware('permission:tours.delete')
            ->name('admin.tours.destroy');
        
        Route::post('/{tour}/publish', [TourController::class, 'publish'])
            ->middleware('permission:tours.publish')
            ->name('admin.tours.publish');
        
        Route::post('/{tour}/unpublish', [TourController::class, 'unpublish'])
            ->middleware('permission:tours.publish')
            ->name('admin.tours.unpublish');
        
        Route::post('/{tour}/dates', [TourController::class, 'addDate'])
            ->middleware('permission:tours.manage_dates')
            ->name('admin.tours.dates.store');
        
        Route::put('/dates/{date}', [TourController::class, 'updateDate'])
            ->middleware('permission:tours.manage_dates')
            ->name('admin.tours.dates.update');
        
        Route::delete('/dates/{date}', [TourController::class, 'deleteDate'])
            ->middleware('permission:tours.manage_dates')
            ->name('admin.tours.dates.destroy');
    });
