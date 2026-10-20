<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // تبدیل همه foreign key های NOT NULL به nullable
            // برای سازگاری با معماری جدید (Order-based refunds)
            
            $table->foreignId('booking_id')->nullable()->change();
            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('processed_by')->nullable()->change();
            $table->foreignId('approved_by')->nullable()->change();
            $table->foreignId('rejected_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // فقط booking_id و user_id و processed_by به NOT NULL برمی‌گردند
            // approved_by و rejected_by از ابتدا nullable بوده‌اند
            $table->foreignId('booking_id')->nullable(false)->change();
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreignId('processed_by')->nullable(false)->change();
        });
    }
};
