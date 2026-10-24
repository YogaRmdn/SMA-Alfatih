<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toefl_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('toefl_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('toefl_questions')->cascadeOnDelete();
            $table->string('selected_answer', 10)->nullable();
            $table->timestamp('saved_at')->nullable();
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toefl_answers');
    }
};
