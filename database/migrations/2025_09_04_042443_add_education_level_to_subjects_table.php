<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'education_level')) {
                $table->enum('education_level', ['SMA/MA', 'SMK/MAK', 'Umum'])
                      ->default('Umum');
            }
            if (!Schema::hasColumn('subjects', 'subject_type')) {
                $table->enum('subject_type', ['Wajib', 'Pilihan', 'Produk_Kreatif_Kewirausahaan'])
                      ->default('Pilihan');
            }
            if (!Schema::hasColumn('subjects', 'subject_number')) {
                $table->integer('subject_number')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'education_level')) {
                $table->dropColumn('education_level');
            }
            if (Schema::hasColumn('subjects', 'subject_type')) {
                $table->dropColumn('subject_type');
            }
            if (Schema::hasColumn('subjects', 'subject_number')) {
                $table->dropColumn('subject_number');
            }
        });
    }
};

