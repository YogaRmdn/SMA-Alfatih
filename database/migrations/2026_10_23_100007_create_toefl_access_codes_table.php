<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toefl_access_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('toefl_students')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('toefl_exams')->cascadeOnDelete();
            $table->string('code_hash');
            $table->string('code_prefix', 6)->nullable(); // untuk pencarian admin (bukan kode asli)
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['exam_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toefl_access_codes');
    }
};
