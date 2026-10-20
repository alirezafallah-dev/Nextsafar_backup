<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // درگاه
            $table->string('gateway', 30)->default('zarinpal'); // zarinpal, idpay, wallet, manual
            
            // مبلغ
            $table->decimal('amount', 14, 2);
            $table->string('currency', 3)->default('IRR');
            
            // وضعیت
            $table->string('status', 20)->default('pending');
            
            // کدهای درگاه
            $table->string('authority_code', 100)->nullable();
            $table->string('transaction_id', 100)->unique()->nullable();
            $table->string('reference_id', 100)->nullable();
            
            // اطلاعات کارت
            $table->string('card_number', 20)->nullable();
            $table->string('card_hash', 64)->nullable();
            
            // اطلاعات درخواست
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // داده‌های درگاه
            $table->jsonb('gateway_request')->nullable();
            $table->jsonb('gateway_response')->nullable();
            $table->jsonb('callback_data')->nullable();
            
            // توضیحات
            $table->text('description')->nullable();
            
            // زمان پرداخت
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
            
            $table->index('booking_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('authority_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
