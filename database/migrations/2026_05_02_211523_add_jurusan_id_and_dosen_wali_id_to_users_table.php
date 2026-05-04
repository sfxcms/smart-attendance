<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('jurusan_id')
                ->nullable()
                ->constrained('jurusans')
                ->nullOnDelete();

            $table->foreignId('dosen_wali_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
            $table->dropForeign(['dosen_wali_id']);
            $table->dropColumn(['jurusan_id', 'dosen_wali_id']);
        });
    }
};
