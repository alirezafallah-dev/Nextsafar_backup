<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('نام نمایشی نقش');
            $table->string('slug', 50)->unique()->comment('شناسه یکتا');
            $table->text('description')->nullable()->comment('توضیحات نقش');
            $table->boolean('is_system')->default(false)->comment('نقش سیستمی (غیرقابل حذف)');
            $table->unsignedInteger('priority')->default(0)->comment('اولویت (برای سلسله مراتب)');
            $table->boolean('requires_2fa')->default(false)->comment('نیاز به 2FA');
            $table->boolean('is_active')->default(true)->comment('فعال بودن نقش');
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_system');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
