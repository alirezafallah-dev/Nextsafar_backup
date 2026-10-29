<?php

namespace App\Modules\WordPress\Providers;

use App\Modules\WordPress\Services\ArticleService;
use App\Modules\WordPress\Services\VisaService;
use App\Modules\WordPress\Services\WordPressClient;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class WordPressServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ثبت سرویس‌ها به صورت singleton
        $this->app->singleton(WordPressClient::class);
        $this->app->singleton(VisaService::class);
        $this->app->singleton(ArticleService::class);
        
        // TourService removed - tours now managed in Laravel Tour module
    }

    public function boot(): void
    {
        // بارگذاری route ها با پیشوند api
        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/../routes.php');
    }
}
