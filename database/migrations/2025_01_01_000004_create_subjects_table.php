<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_required')->default(false); // Mata pelajaran wajib
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default subjects
        DB::table('subjects')->insert([
            ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'is_required' => true, 'is_active' => true],
            ['name' => 'Matematika', 'code' => 'MTK', 'is_required' => true, 'is_active' => true],
            ['name' => 'Bahasa Inggris', 'code' => 'BIG', 'is_required' => true, 'is_active' => true],
            ['name' => 'Fisika', 'code' => 'FIS', 'is_required' => false, 'is_active' => true],
            ['name' => 'Kimia', 'code' => 'KIM', 'is_required' => false, 'is_active' => true],
            ['name' => 'Biologi', 'code' => 'BIO', 'is_required' => false, 'is_active' => true],
            ['name' => 'Ekonomi', 'code' => 'EKO', 'is_required' => false, 'is_active' => true],
            ['name' => 'Sejarah', 'code' => 'SEJ', 'is_required' => false, 'is_active' => true],
            ['name' => 'Geografi', 'code' => 'GEO', 'is_required' => false, 'is_active' => true],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
