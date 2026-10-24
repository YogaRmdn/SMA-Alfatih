<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toefl_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('toefl_exams')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('toefl_students')->cascadeOnDelete();
            $table->foreignId('access_code_id')->nullable()->constrained('toefl_access_codes')->nullOnDelete();
            $table->string('status', 20)->default('in_progress')->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('deadline_at')->nullable()->index();
            $table->timestamp('submitted_at')->nullable();
            $table->string('current_section_type', 30)->nullable();
            $table->unsignedInteger('session_version')->default(1);
            $table->timestamps();

            /*
            | Satu attempt per siswa per ujian — sekaligus menjamin maksimal
            | satu sesi aktif. Koneksi terputus cukup melanjutkan attempt ini
            | (resume), bukan membuat yang baru.
            */
            $table->unique(['exam_id', 'student_id'], 'toefl_attempt_one_per_student');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toefl_attempts');
    }
};
