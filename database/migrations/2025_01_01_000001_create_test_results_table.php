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
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->json('subjects'); // Array mata pelajaran yang diujikan
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->enum('status', ['ongoing', 'completed', 'expired'])->default('ongoing');
            $table->json('scores')->nullable(); // Skor per mata pelajaran
            $table->decimal('total_score', 5, 2)->nullable(); // Total skor keseluruhan
            $table->json('recommendations')->nullable(); // Rekomendasi jurusan
            $table->timestamps();
            
            $table->index(['student_id', 'status']);
            $table->index('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
