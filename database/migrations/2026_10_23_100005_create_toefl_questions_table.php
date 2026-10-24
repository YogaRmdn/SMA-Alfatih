<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toefl_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('toefl_sections')->cascadeOnDelete();
            $table->foreignId('passage_id')->nullable()->constrained('toefl_passages')->nullOnDelete();
            $table->foreignId('audio_asset_id')->nullable();
            $table->string('question_type', 40)->default('multiple_choice');
            $table->text('question_text');
            $table->json('options');
            $table->string('answer_key', 10);
            $table->unsignedInteger('points')->default(1);
            $table->unsignedInteger('question_order')->default(1);
            $table->timestamps();

            $table->index(['section_id', 'question_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toefl_questions');
    }
};
