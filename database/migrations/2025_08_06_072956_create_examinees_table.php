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
        Schema::create('examinees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('token_id')->constrained();
            $table->string('rank');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('qualifier')->nullable();
            $table->string('designation');
            $table->string('unit');
            $table->string('subunit');
            $table->string('station')->nullable();
            $table->boolean('accepted_privacy')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examinees');
    }
};
