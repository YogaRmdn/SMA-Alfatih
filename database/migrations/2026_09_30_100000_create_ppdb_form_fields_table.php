<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_form_fields', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->string('label');
            $table->string('type', 20)->default('text');
            $table->string('group_label')->nullable();
            $table->string('width', 10)->default('full');
            $table->string('placeholder')->nullable();
            $table->string('help_text', 500)->nullable();
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(true);
            $table->string('accept')->nullable();
            $table->unsignedInteger('max_kb')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('ppdb', function (Blueprint $table) {
            $table->string('program', 50)->nullable()->after('origin_school');
            $table->string('info_source', 100)->nullable()->after('program');
            $table->json('answers')->nullable()->after('report_card');
        });
    }

    public function down(): void
    {
        Schema::table('ppdb', function (Blueprint $table) {
            $table->dropColumn(['program', 'info_source', 'answers']);
        });

        Schema::dropIfExists('ppdb_form_fields');
    }
};
