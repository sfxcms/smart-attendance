<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->string('tipe_sesi', 10)->default('offline')->after('status');
            $table->string('link_meeting')->nullable()->after('tipe_sesi');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_sessions', function (Blueprint $table) {
            $table->dropColumn(['tipe_sesi', 'link_meeting']);
        });
    }
};
