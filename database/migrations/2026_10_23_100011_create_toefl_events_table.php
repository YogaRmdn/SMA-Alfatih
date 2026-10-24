<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toefl_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('toefl_attempts')->cascadeOnDelete();
            $table->string('event_type', 40)->index();
            $table->timestamp('event_timestamp')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toefl_events');
    }
};
