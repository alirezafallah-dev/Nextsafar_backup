<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_temp_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            
            $table->string('token', 100)->unique()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            // === محدودیت‌ها ===
            $table->unsignedSmallInteger('max_uses')->default(1);
            $table->unsignedSmallInteger('uses_count')->default(0);
            $table->timestamp('expires_at');
            $table->boolean('is_revoked')->default(false);
            
            // === امنیت ===
            $table->string('allowed_ip', 45)->nullable();
            $table->foreignId('allowed_user_id')->nullable();
            
            $table->timestamps();
            
            $table->index(['token', 'expires_at']);
            $table->index(['document_id', 'is_revoked']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_temp_links');
    }
};
