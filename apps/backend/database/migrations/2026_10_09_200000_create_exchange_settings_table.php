<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_settings', function (Blueprint $table) {
            $table->id();
            
            // API Key برای BrsApi
            $table->string('api_key', 255)->nullable();
            
            // نرخ‌های ذخیره شده (JSONB)
            $table->jsonb('rates')->nullable();
            
            // نرخ‌های دستی (fallback)
            $table->jsonb('manual_rates')->nullable();
            
            // زمان آخرین بروزرسانی
            $table->timestamp('last_updated_at')->nullable();
            
            // مدت کش (ثانیه) - پیش‌فرض 1 ساعت
            $table->unsignedInteger('cache_duration')->default(3600);
            
            // آیا از نرخ زنده استفاده شود؟
            $table->boolean('use_live_rates')->default(true);
            
            $table->timestamps();
        });
        
        // ایجاد یک رکورد پیش‌فرض
        DB::table('exchange_settings')->insert([
            'api_key' => null,
            'rates' => null,
            'manual_rates' => json_encode([
                'USD' => 600000,
                'EUR' => 650000,
                'GBP' => 750000,
                'AED' => 163000,
                'TRY' => 18000,
                'THB' => 17000,
                'CNY' => 83000,
                'RUB' => 6500,
                'OMR' => 1560000,
                'QAR' => 165000,
                'IQD' => 460,
                'MYR' => 135000,
            ]),
            'last_updated_at' => null,
            'cache_duration' => 3600,
            'use_live_rates' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_settings');
    }
};
