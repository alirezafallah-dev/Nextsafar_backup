<?php

use App\Modules\Ledger\Controllers\LedgerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Ledger Routes (Admin Only - Financial Module)
|--------------------------------------------------------------------------
*/

Route::prefix('v1/admin/ledger')
    ->middleware(['auth:sanctum', 'log.admin'])
    ->group(function () {
        
        // === تراکنش‌ها ===
        Route::get('/entries', [LedgerController::class, 'index'])
            ->middleware('permission:logs.view_system')
            ->name('admin.ledger.entries');
        
        Route::get('/entries/{entry}', [LedgerController::class, 'show'])
            ->middleware('permission:logs.view_system')
            ->name('admin.ledger.entries.show');
        
        Route::get('/batch/{batchId}', [LedgerController::class, 'batch'])
            ->middleware('permission:logs.view_system')
            ->name('admin.ledger.batch');
        
        Route::post('/entries', [LedgerController::class, 'store'])
            ->middleware('permission:settings.edit')
            ->name('admin.ledger.entries.store');
        
        Route::post('/entries/{entry}/reverse', [LedgerController::class, 'reverse'])
            ->middleware('permission:settings.edit')
            ->name('admin.ledger.entries.reverse');
        
        // === حساب‌ها ===
        Route::get('/accounts', [LedgerController::class, 'accounts'])
            ->middleware('permission:logs.view_system')
            ->name('admin.ledger.accounts');
        
        Route::get('/accounts/{code}/statement', [LedgerController::class, 'accountStatement'])
            ->middleware('permission:logs.view_system')
            ->name('admin.ledger.accounts.statement');
        
        // === گزارش‌ها ===
        Route::get('/reports/trial-balance', [LedgerController::class, 'trialBalance'])
            ->middleware('permission:reports.view_financial')
            ->name('admin.ledger.reports.trial_balance');
        
        Route::get('/reports/income-statement', [LedgerController::class, 'incomeStatement'])
            ->middleware('permission:reports.view_financial')
            ->name('admin.ledger.reports.income_statement');
        
        Route::get('/reports/balance-sheet', [LedgerController::class, 'balanceSheet'])
            ->middleware('permission:reports.view_financial')
            ->name('admin.ledger.reports.balance_sheet');
    });
