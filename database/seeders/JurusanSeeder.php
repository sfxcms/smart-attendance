<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('jurusans')->insert([
            ['nama' => 'Administrasi Bisnis', 'kode' => 'AB', 'created_at' => $now, 'updated_at' => $now],
            ['nama' => 'Ilmu Pemerintahan', 'kode' => 'IP', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
