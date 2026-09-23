<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppdb', function (Blueprint $table) {
            $table->string('access_code', 16)->nullable()->index()->after('registration_number');
        });
    }

    public function down(): void
    {
        Schema::table('ppdb', function (Blueprint $table) {
            $table->dropIndex(['access_code']);
            $table->dropColumn('access_code');
        });
    }
};
