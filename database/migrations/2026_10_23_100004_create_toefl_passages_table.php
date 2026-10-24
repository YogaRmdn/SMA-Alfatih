<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toefl_passages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('toefl_sections')->cascadeOnDelete();
            $table->text('passage_text');
            $table->string('title', 160)->nullable();
            $table->unsignedInteger('passage_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toefl_passages');
    }
};
