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
        Schema::table('enrollments', function (Blueprint $table) {
            // Drop existing foreign key and index
            $table->dropForeign(['course_id']);
            $table->dropUnique(['user_id', 'course_id']);
            $table->dropColumn('course_id');

            // Add new columns
            $table->foreignId('jurusan_id')->after('user_id')->constrained('jurusans')->cascadeOnDelete();
            $table->integer('semester')->after('jurusan_id');

            // New unique constraint
            $table->unique(['user_id', 'jurusan_id', 'semester']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Drop new columns and constraints
            $table->dropForeign(['jurusan_id']);
            $table->dropUnique(['user_id', 'jurusan_id', 'semester']);
            $table->dropColumn(['jurusan_id', 'semester']);

            // Restore original
            $table->foreignId('course_id')->after('user_id')->constrained('courses')->cascadeOnDelete();
            $table->unique(['user_id', 'course_id']);
        });
    }
};
