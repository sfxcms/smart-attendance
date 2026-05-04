<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseLecturerSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $abDosen = User::where('role', 'dosen')->whereHas('jurusan', fn($q) => $q->where('kode', 'AB'))->get();
        $ipDosen = User::where('role', 'dosen')->whereHas('jurusan', fn($q) => $q->where('kode', 'IP'))->get();

        $abCourses = Course::whereHas('jurusan', fn($q) => $q->where('kode', 'AB'))->get();
        $ipCourses = Course::whereHas('jurusan', fn($q) => $q->where('kode', 'IP'))->get();

        $assignments = [];

        // Assign AB courses to AB dosen (round-robin)
        foreach ($abCourses as $i => $course) {
            $dosen = $abDosen[$i % $abDosen->count()];
            $assignments[] = [
                'course_id' => $course->id,
                'user_id' => $dosen->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Assign IP courses to IP dosen (round-robin)
        foreach ($ipCourses as $i => $course) {
            $dosen = $ipDosen[$i % $ipDosen->count()];
            $assignments[] = [
                'course_id' => $course->id,
                'user_id' => $dosen->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($assignments, 50) as $chunk) {
            DB::table('course_lecturer')->insert($chunk);
        }
    }
}
