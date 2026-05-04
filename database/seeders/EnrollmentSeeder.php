<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $abJurusan = Jurusan::where('kode', 'AB')->first();
        $ipJurusan = Jurusan::where('kode', 'IP')->first();

        $abStudents = User::where('role', 'mahasiswa')
            ->whereHas('jurusan', fn($q) => $q->where('kode', 'AB'))
            ->get();

        $ipStudents = User::where('role', 'mahasiswa')
            ->whereHas('jurusan', fn($q) => $q->where('kode', 'IP'))
            ->get();

        $enrollments = [];

        // Enroll AB students in AB jurusan, semester 1
        if ($abJurusan) {
            foreach ($abStudents as $student) {
                $enrollments[] = [
                    'user_id' => $student->id,
                    'jurusan_id' => $abJurusan->id,
                    'semester' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Enroll IP students in IP jurusan, semester 1
        if ($ipJurusan) {
            foreach ($ipStudents as $student) {
                $enrollments[] = [
                    'user_id' => $student->id,
                    'jurusan_id' => $ipJurusan->id,
                    'semester' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($enrollments, 50) as $chunk) {
            DB::table('enrollments')->insert($chunk);
        }
    }
}
