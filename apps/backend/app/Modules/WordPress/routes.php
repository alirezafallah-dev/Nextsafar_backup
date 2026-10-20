<?php

use App\Modules\WordPress\Controllers\ArticleController;
use App\Modules\WordPress\Controllers\VisaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WordPress Content Routes
|--------------------------------------------------------------------------
|
| این route ها فقط محتوای وردپرس را ارائه می‌دهند:
| - ویزاها (از پست تایپ visa)
| - اخبار سفر (از پست تایپ travelnews)
| - راهنماهای سفر (از پست تایپ travelguide)
|
| ⚠️ توجه: تورها از ماژول Tour در لاراول مدیریت می‌شوند
| مسیر تورها: api/v1/tours
|
*/

// ویزاها
Route::prefix('v1/visas')->group(function () {
    Route::get('/', [VisaController::class, 'index']);
    Route::get('/popular/list', [VisaController::class, 'popular']);
    Route::get('/search', [VisaController::class, 'search']);
    Route::get('/{slug}', [VisaController::class, 'show']);
});

// اخبار سفر
Route::prefix('v1/news')->group(function () {
    Route::get('/', [ArticleController::class, 'news']);
    Route::get('/{slug}', [ArticleController::class, 'newsDetail']);
});

// راهنماهای سفر
Route::prefix('v1/guides')->group(function () {
    Route::get('/', [ArticleController::class, 'guides']);
    Route::get('/{slug}', [ArticleController::class, 'guideDetail']);
});

// جستجوی مقالات
Route::get('/v1/articles/search', [ArticleController::class, 'search']);
