<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toefl_audio_play_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('toefl_attempts')->cascadeOnDelete();
            $table->foreignId('audio_asset_id')->constrained('toefl_audio_assets')->cascadeOnDelete();
            $table->unsignedInteger('play_number');
            $table->timestamp('started_at')->nullable();
            $table->timestamps();

            // Penghitung per percobaan: 1 x play_number = batas terlampaui.
            $table->unique(['attempt_id', 'audio_asset_id', 'play_number'], 'toefl_play_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toefl_audio_play_events');
    }
};
