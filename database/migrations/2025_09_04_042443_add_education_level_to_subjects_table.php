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
        Schema::table('subjects', function (Blueprint $table) {
            // Tambahkan kolom untuk jenjang pendidikan
            $table->enum('education_level', ['SMA/MA', 'SMK/MAK', 'Umum'])->default('Umum')->after('code');
            $table->enum('subject_type', ['Wajib', 'Pilihan', 'Produk_Kreatif_Kewirausahaan'])->default('Pilihan')->after('education_level');
            $table->integer('subject_number')->nullable()->after('subject_type'); // Nomor mata pelajaran sesuai ketentuan (1-19)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['education_level', 'subject_type', 'subject_number']);
        });
    }
};