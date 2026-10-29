<?php

use App\Modules\Visa\Controllers\VisaCaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Visa Routes
|--------------------------------------------------------------------------
*/

// === PUBLIC ENDPOINTS (no auth) ===
Route::prefix('v1/visa-types')->group(function () {
    Route::get('/', [\App\Modules\Visa\Controllers\VisaTypeController::class, 'index'])
        ->name('visa-types.index');
    
    Route::get('/{slug}', [\App\Modules\Visa\Controllers\VisaTypeController::class, 'show'])
        ->name('visa-types.show');
});

// === USER ENDPOINTS (auth required) ===
Route::prefix('v1/visa-cases')
    ->middleware('auth:sanctum')
    ->group(function () {
        
        Route::post('/', [VisaCaseController::class, 'store'])
            ->name('visa-cases.store');
        
        Route::get('/', [VisaCaseController::class, 'index'])
            ->name('visa-cases.index');
        
        Route::get('/{case}', [VisaCaseController::class, 'show'])
            ->name('visa-cases.show');
        
        Route::post('/{case}/passengers', [VisaCaseController::class, 'addPassenger'])
            ->name('visa-cases.passengers.store');
        
        Route::put('/{case}/passengers/{passenger}', [VisaCaseController::class, 'updatePassenger'])
            ->name('visa-cases.passengers.update');
        
        Route::post('/{case}/documents', [VisaCaseController::class, 'attachDocument'])
            ->name('visa-cases.documents.attach');
        
        Route::get('/{case}/checklist', [VisaCaseController::class, 'checklist'])
            ->name('visa-cases.checklist');
        
        Route::post('/{case}/notes', [VisaCaseController::class, 'addNote'])
            ->name('visa-cases.notes.store');
    });

// === ADMIN ENDPOINTS ===
Route::prefix('v1/admin/visa-cases')
    ->middleware(['auth:sanctum', 'log.admin'])
    ->group(function () {
        
        Route::get('/', [VisaCaseController::class, 'adminIndex'])
            ->middleware('permission:visa.view')
            ->name('admin.visa-cases.index');
        
        Route::get('/statistics', [VisaCaseController::class, 'statistics'])
            ->middleware('permission:reports.view_dashboard')
            ->name('admin.visa-cases.statistics');
        
        Route::get('/{case}', [VisaCaseController::class, 'adminShow'])
            ->middleware('permission:visa.view')
            ->name('admin.visa-cases.show');
        
        Route::post('/{case}/status', [VisaCaseController::class, 'changeStatus'])
            ->middleware('permission:visa.manage')
            ->name('admin.visa-cases.change_status');
        
        Route::post('/{case}/assign', [VisaCaseController::class, 'assign'])
            ->middleware('permission:visa.assign')
            ->name('admin.visa-cases.assign');
        
        Route::post('/{case}/documents/{doc}/verify', [VisaCaseController::class, 'verifyDocument'])
            ->middleware('permission:visa.manage')
            ->name('admin.visa-cases.documents.verify');
        
        Route::post('/{case}/appointment', [VisaCaseController::class, 'bookAppointment'])
            ->middleware('permission:visa.manage')
            ->name('admin.visa-cases.book_appointment');
        
        Route::post('/{case}/notes', [VisaCaseController::class, 'adminAddNote'])
            ->middleware('permission:visa.manage')
            ->name('admin.visa-cases.notes.store');
        
        Route::get('/{case}/timeline', [VisaCaseController::class, 'timeline'])
            ->middleware('permission:visa.view')
            ->name('admin.visa-cases.timeline');
        
        Route::post('/from-lead/{lead}', [VisaCaseController::class, 'createFromLead'])
            ->middleware('permission:visa.manage')
            ->name('admin.visa-cases.from_lead');
    });
