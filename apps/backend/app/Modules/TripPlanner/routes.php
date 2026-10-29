<?php

use Illuminate\Support\Facades\Route;
use App\Modules\TripPlanner\Controllers\TripPlannerController;

Route::prefix('v1/trip-planner')->group(function () {
    Route::post('/generate', [TripPlannerController::class, 'generate'])
        ->name('trip-planner.generate')
        ->middleware('throttle:10,1');

    Route::get('/status', [TripPlannerController::class, 'status'])
        ->name('trip-planner.status');

    Route::get('/{id}', [TripPlannerController::class, 'show'])
        ->name('trip-planner.show')
        ->where('id', '[0-9]+');
});
