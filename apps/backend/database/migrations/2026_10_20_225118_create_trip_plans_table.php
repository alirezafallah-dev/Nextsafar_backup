<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AI Trip Planner Table
     * Migrated from WordPress (inc/database/ai-trip-table.php)
     */
    public function up(): void
    {
        Schema::create('trip_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            
            // Input
            $table->string('destination', 100);
            $table->string('country', 100)->nullable();
            $table->unsignedInteger('days')->default(3);
            $table->enum('budget_level', ['economy', 'medium', 'luxury'])->default('medium');
            $table->json('interests')->nullable();
            $table->unsignedInteger('travelers')->default(2);
            $table->date('start_date')->nullable();
            
            // Output
            $table->string('trip_title')->nullable();
            $table->text('trip_summary')->nullable();
            $table->json('days_plan');
            $table->bigInteger('total_budget_min')->nullable();
            $table->bigInteger('total_budget_max')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->json('suggested_hotels')->nullable();
            $table->json('suggested_tours')->nullable();
            $table->json('suggested_restaurants')->nullable();
            
            // Meta
            $table->string('model_used', 50)->nullable();
            $table->unsignedInteger('tokens_used')->default(0);
            $table->unsignedInteger('generation_time_ms')->default(0);
            $table->enum('status', ['pending', 'completed', 'failed', 'expired'])->default('pending');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('error_message')->nullable();
            
            $table->timestamps();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_viewed_at')->nullable();
            $table->unsignedInteger('view_count')->default(1);
            
            $table->index(['user_id', 'status']);
            $table->index(['session_id', 'status']);
            $table->index('destination');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_plans');
    }
};
