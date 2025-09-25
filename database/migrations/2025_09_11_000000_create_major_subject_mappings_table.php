<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('major_subject_mappings')) {
            Schema::create('major_subject_mappings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('major_id');
                $table->unsignedBigInteger('subject_id');
                $table->string('education_level');
                $table->string('mapping_type')->default('optional');
                $table->integer('priority')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('major_subject_mappings');
    }
};
