<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            if (!Schema::hasColumn('tours', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('tours', 'is_published')) {
                $table->index('is_published');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            if (Schema::hasColumn('tours', 'is_published')) {
                $table->dropColumn('is_published');
            }
        });
    }
};
