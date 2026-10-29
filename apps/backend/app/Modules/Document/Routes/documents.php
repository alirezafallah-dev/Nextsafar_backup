<?php

use App\Modules\Document\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Document Routes (Section 17 of spec)
|--------------------------------------------------------------------------
*/

// === PUBLIC ENDPOINTS (no auth) ===
Route::prefix('v1/documents')->group(function () {
    // دسترسی از طریق لینک زمان‌دار
    Route::get('/temp/{token}', [DocumentController::class, 'accessViaTempLink'])
        ->name('documents.temp');
    
    // دسترسی به فایل عمومی با UUID
    Route::get('/public/{uuid}', [DocumentController::class, 'accessPublic'])
        ->name('documents.public');
});

// === USER ENDPOINTS (auth required) ===
Route::prefix('v1/documents')
    ->middleware('auth:sanctum')
    ->group(function () {
        
        Route::post('/', [DocumentController::class, 'store'])
            ->name('documents.store');
        
        Route::get('/', [DocumentController::class, 'index'])
            ->name('documents.index');
        
        Route::get('/{document}', [DocumentController::class, 'show'])
            ->name('documents.show');
        
        Route::get('/{document}/download', [DocumentController::class, 'download'])
            ->name('documents.download');
        
        Route::post('/{document}/temp-link', [DocumentController::class, 'createTempLink'])
            ->name('documents.create_temp_link');
        
        Route::delete('/{document}', [DocumentController::class, 'destroy'])
            ->name('documents.destroy');
    });

// === ADMIN ENDPOINTS ===
Route::prefix('v1/admin/documents')
    ->middleware(['auth:sanctum', 'log.admin'])
    ->group(function () {
        
        Route::get('/', [DocumentController::class, 'adminIndex'])
            ->middleware('permission:documents.view_all')
            ->name('admin.documents.index');
        
        Route::get('/statistics', [DocumentController::class, 'statistics'])
            ->middleware('permission:reports.view_dashboard')
            ->name('admin.documents.statistics');
        
        Route::post('/{document}/mark-clean', [DocumentController::class, 'markClean'])
            ->middleware('permission:documents.verify')
            ->name('admin.documents.mark_clean');
        
        Route::post('/{document}/mark-infected', [DocumentController::class, 'markInfected'])
            ->middleware('permission:documents.verify')
            ->name('admin.documents.mark_infected');
        
        Route::get('/{document}/access-logs', [DocumentController::class, 'accessLogs'])
            ->middleware('permission:logs.view_system')
            ->name('admin.documents.access_logs');
        
        Route::post('/temp-links/{link}/revoke', [DocumentController::class, 'revokeTempLink'])
            ->middleware('permission:documents.verify')
            ->name('admin.documents.revoke_link');
    });
