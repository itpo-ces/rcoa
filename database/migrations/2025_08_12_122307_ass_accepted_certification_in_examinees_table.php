<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('examinees', function (Blueprint $table) {
            $table->boolean('accepted_certification')->after('accepted_privacy')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examinees', function (Blueprint $table) {
            $table->dropColumn('accepted_certification');
        });
    }
};
