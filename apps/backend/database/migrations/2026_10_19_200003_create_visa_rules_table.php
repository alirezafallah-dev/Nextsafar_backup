<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visa_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_type_id')->constrained('visa_types')->cascadeOnDelete();
            
            // === قوانین ===
            $table->string('rule_key', 100)->comment('کلید قانون (age_limit, bank_statement, etc.)');
            $table->string('title', 255);
            $table->text('description')->nullable();
            
            // === مقدار ===
            $table->enum('value_type', ['boolean', 'number', 'text', 'json'])->default('text');
            $table->text('value')->nullable();
            
            // === اهمیت ===
            $table->enum('importance', ['info', 'warning', 'critical'])->default('info');
            $table->boolean('is_mandatory')->default(false);
            
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['visa_type_id', 'rule_key']);
            $table->index('importance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_rules');
    }
};
