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
        Schema::table('exams', function (Blueprint $table) {
            // Remove unique constraint from exam_date
            $table->dropUnique('exams_exam_date_unique');
            
            // Add unique constraint to title
            $table->unique('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Reverse the changes
            $table->dropUnique('exams_title_unique');
            $table->unique('exam_date');
        });
    }
};