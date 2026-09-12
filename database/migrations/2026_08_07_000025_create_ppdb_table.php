<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('full_name');
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion')->default('Islam');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('nisn')->nullable();
            $table->string('origin_school')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('father_job')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('family_income')->nullable();
            $table->string('photo')->nullable();
            $table->string('kk')->nullable();
            $table->string('birth_certificate')->nullable();
            $table->string('diploma')->nullable();
            $table->string('report_card')->nullable();
            $table->enum('status', ['pending', 'verified', 'lulus_administrasi', 'rejected', 'accepted'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->string('academic_year')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb');
    }
};
