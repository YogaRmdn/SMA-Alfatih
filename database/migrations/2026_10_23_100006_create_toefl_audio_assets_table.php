<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toefl_audio_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('toefl_sections')->cascadeOnDelete();
            $table->string('label', 160)->nullable();
            $table->string('storage_path');
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toefl_audio_assets');
    }
};
