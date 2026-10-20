<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->index();
            $table->string('code_hash', 64);
            $table->string('purpose', 50)->default('login');
            $table->unsignedInteger('attempts')->default(0);
            $table->string('ip_address', 45);
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['phone', 'expires_at']);
            $table->index(['ip_address', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
