<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaires', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->text('instructions')->nullable();
            $table->string('version', 20)->default('1.0');
            $table->string('status', 20)->default('draft')->index();
            $table->boolean('allow_retake')->default(false);
            $table->boolean('needs_verification')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaires');
    }
};
