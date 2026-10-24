<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toefl_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->unique()->constrained('toefl_attempts')->cascadeOnDelete();
            $table->unsignedInteger('listening_raw_score')->default(0);
            $table->unsignedInteger('structure_raw_score')->default(0);
            $table->unsignedInteger('reading_raw_score')->default(0);
            $table->unsignedInteger('total_correct')->default(0);
            $table->unsignedInteger('total_questions')->default(0);
            $table->unsignedInteger('scaled_score')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toefl_results');
    }
};
