<?php

use App\Modules\WordPress\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WordPress Webhook Routes
|--------------------------------------------------------------------------
| These routes receive webhooks from WordPress.
| Protected by signature verification middleware.
*/

Route::prefix('v1/webhooks/wordpress')->group(function () {
    
    Route::post('/hotel', [WebhookController::class, 'hotel'])
        ->middleware(\App\Http\Middleware\VerifyWebhookSignature::class . ':wordpress')
        ->name('webhooks.wordpress.hotel');
    
    Route::get('/health', [WebhookController::class, 'health'])
        ->name('webhooks.wordpress.health');
    
});
