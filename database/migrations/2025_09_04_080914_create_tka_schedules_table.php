<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tka_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul jadwal TKA
            $table->text('description')->nullable(); // Deskripsi jadwal
            $table->datetime('start_date'); // Tanggal dan waktu mulai
            $table->datetime('end_date'); // Tanggal dan waktu selesai
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->enum('type', ['regular', 'makeup', 'special'])->default('regular'); // Jenis TKA
            $table->text('instructions')->nullable(); // Instruksi khusus
            $table->json('target_schools')->nullable(); // Sekolah yang ditargetkan (null = semua sekolah)
            $table->boolean('is_active')->default(true);
            $table->string('created_by')->nullable(); // Admin yang membuat jadwal
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['start_date', 'end_date']);
            $table->index(['status', 'is_active']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tka_schedules');
    }
};
