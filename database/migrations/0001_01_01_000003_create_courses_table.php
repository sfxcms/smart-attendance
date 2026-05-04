<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mk', 20);
            $table->string('nama_mk');
            $table->unsignedSmallInteger('sks')->default(1);
            $table->unsignedSmallInteger('semester');
            $table->timestamps();

            $table->unique('kode_mk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
