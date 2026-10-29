<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('passenger_id')->nullable()->constrained('booking_passengers')->nullOnDelete();
            
            // نوع مدرک
            $table->string('document_type', 50); // passport, photo, invitation, bank_statement, ...
            $table->string('title')->nullable();
            
            // فایل
            $table->string('file_path');
            $table->string('file_url');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size'); // bytes
            
            // وضعیت
            $table->string('status', 20)->default('uploaded'); // uploaded, verified, rejected
            $table->text('rejection_reason')->nullable();
            
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
            
            $table->index('booking_id');
            $table->index('document_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_documents');
    }
};
