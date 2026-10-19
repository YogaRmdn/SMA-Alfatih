<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->unique()->constrained('attempts')->cascadeOnDelete();
            $table->unsignedInteger('score_a')->default(0);
            $table->unsignedInteger('score_b')->default(0);
            $table->unsignedInteger('score_c')->default(0);
            $table->decimal('percentage_a', 5, 2)->default(0);
            $table->decimal('percentage_b', 5, 2)->default(0);
            $table->decimal('percentage_c', 5, 2)->default(0);
            $table->json('dominant_categories');
            $table->string('result_status', 20)->default('dominant');
            $table->text('interpretation')->nullable();
            $table->text('recommendation')->nullable();
            $table->string('questionnaire_version', 20)->nullable();
            $table->text('teacher_note')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
