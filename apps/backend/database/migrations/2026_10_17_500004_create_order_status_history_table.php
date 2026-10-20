<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->text('reason')->nullable();
            
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('changed_by_name', 150)->nullable();
            $table->string('change_source', 50)->default('manual')
                ->comment('manual, system, payment_callback, user_request');
            
            $table->string('ip_address', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            
            // === Indexes ===
            $table->index(['order_id', 'changed_at']);
            $table->index('new_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
    }
};
