<?php

use App\Modules\Lead\Controllers\LeadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Lead Routes
|--------------------------------------------------------------------------
*/

// === Public Routes ===
Route::prefix('v1/leads')->group(function () {
    Route::post('/', [LeadController::class, 'store'])
        ->name('leads.store');
});

// === Admin Routes ===
Route::prefix('v1/admin/leads')
    ->middleware(['auth:sanctum', 'log.admin'])
    ->group(function () {
        
        // CRUD
        Route::get('/', [LeadController::class, 'index'])
            ->middleware('permission:leads.view')
            ->name('admin.leads.index');
        
        Route::get('/my', [LeadController::class, 'myLeads'])
            ->middleware('permission:leads.view_own')
            ->name('admin.leads.my');
        
        Route::get('/due-follow-up', [LeadController::class, 'dueFollowUp'])
            ->middleware('permission:leads.view_own')
            ->name('admin.leads.due_follow_up');
        
        Route::get('/statistics', [LeadController::class, 'statistics'])
            ->middleware('permission:reports.view_leads')
            ->name('admin.leads.statistics');
        
        Route::get('/{lead}', [LeadController::class, 'show'])
            ->middleware('permission:leads.view')
            ->name('admin.leads.show');
        
        Route::put('/{lead}', [LeadController::class, 'update'])
            ->middleware('permission:leads.edit')
            ->name('admin.leads.update');
        
        Route::delete('/{lead}', [LeadController::class, 'destroy'])
            ->middleware('permission:leads.edit')
            ->name('admin.leads.destroy');
        
        // Actions
        Route::post('/{lead}/status', [LeadController::class, 'changeStatus'])
            ->middleware('permission:leads.change_status')
            ->name('admin.leads.change_status');
        
        Route::post('/{lead}/assign', [LeadController::class, 'assign'])
            ->middleware('permission:leads.assign')
            ->name('admin.leads.assign');
        
        Route::post('/{lead}/contact', [LeadController::class, 'recordContact'])
            ->middleware('permission:leads.edit')
            ->name('admin.leads.record_contact');
        
        Route::post('/{lead}/notes', [LeadController::class, 'addNote'])
            ->middleware('permission:leads.add_note')
            ->name('admin.leads.add_note');
        
        Route::post('/{lead}/follow-up', [LeadController::class, 'scheduleFollowUp'])
            ->middleware('permission:leads.edit')
            ->name('admin.leads.schedule_follow_up');
        
        Route::post('/{lead}/convert', [LeadController::class, 'convertToBooking'])
            ->middleware('permission:leads.convert_to_booking')
            ->name('admin.leads.convert');
    });
