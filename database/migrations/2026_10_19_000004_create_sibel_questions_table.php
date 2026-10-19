<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_id')->constrained('questionnaires')->cascadeOnDelete();
            $table->text('question_text');
            $table->unsignedInteger('order_number')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->index(['questionnaire_id', 'order_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
