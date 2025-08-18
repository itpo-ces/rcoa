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
            $table->integer('last_question_number')->nullable()->after('station');
            $table->json('exam_progress')->nullable()->after('last_question_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examinees', function (Blueprint $table) {
            $table->dropColumn('last_question_number');
            $table->dropColumn('exam_progress');
        });
    }
};
