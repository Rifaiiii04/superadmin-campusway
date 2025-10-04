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
        if (!Schema::hasTable('program_studi_subjects')) {
            Schema::create('program_studi_subjects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('program_studi_id')->constrained('program_studi')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->enum('kurikulum_type', ['merdeka', '2013_ipa', '2013_ips', '2013_bahasa']);
                $table->boolean('is_required')->default(false);
                $table->timestamps();
                
                // Index untuk performa
                $table->index(['program_studi_id', 'kurikulum_type']);
                $table->index(['subject_id', 'kurikulum_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_studi_subjects');
    }
};