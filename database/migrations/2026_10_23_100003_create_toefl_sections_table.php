<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toefl_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('toefl_exams')->cascadeOnDelete();
            $table->string('section_type', 30)->index();
            $table->string('title', 160);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->unsignedInteger('section_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toefl_sections');
    }
};
