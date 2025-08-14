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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'examiner', 'examinee'])->default('examinee')->after('remember_token');
            $table->string('profile_picture')->nullable()->after('role');
            $table->string('google2fa_secret')->nullable()->after('profile_picture');
            $table->integer('no_of_attempts')->default(0)->after('google2fa_secret');
            $table->boolean('active')->default(true)->after('no_of_attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'profile_picture', 'google2fa_secret', 'no_of_attempts', 'active']);
        });
    }
};
