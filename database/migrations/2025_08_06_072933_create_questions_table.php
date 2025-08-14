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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained();
            $table->text('question_text');
            $table->enum('type', ['multiple_choice', 'fill_in_the_blanks', 'true_or_false', 'yes_or_no']);
            $table->enum('difficulty', ['easy', 'moderate', 'difficult', 'extra difficult']);
            $table->text('correct_answer');
            $table->json('options')->nullable(); // For MCQs
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
