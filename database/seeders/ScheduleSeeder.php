<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $ab = Jurusan::where('kode', 'AB')->first();
        $ip = Jurusan::where('kode', 'IP')->first();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $ruangList = ['Ruang 101', 'Ruang 102', 'Ruang 201', 'Ruang 202', 'Ruang 301', 'Ruang 302', 'Lab A', 'Lab B'];
        $kelompokList = ['A', 'B'];
        $jamList = [
            ['08:00:00', '09:40:00'],
            ['10:00:00', '12:20:00'],
            ['13:00:00', '14:40:00'],
            ['15:00:00', '16:40:00'],
            ['16:50:00', '18:30:00'],
        ];

        $abCourses = DB::table('courses')->where('jurusan_id', $ab->id)->get();
        $ipCourses = DB::table('courses')->where('jurusan_id', $ip->id)->get();

        $schedules = [];

        foreach ($abCourses as $i => $course) {
            $schedules[] = [
                'course_id' => $course->id,
                'hari' => $hariList[$i % count($hariList)],
                'jam_mulai' => $jamList[$i % count($jamList)][0],
                'jam_selesai' => $jamList[$i % count($jamList)][1],
                'ruang' => $ruangList[$i % count($ruangList)],
                'kelompok' => $kelompokList[$i % count($kelompokList)],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($ipCourses as $i => $course) {
            $offset = $i + 3;
            $schedules[] = [
                'course_id' => $course->id,
                'hari' => $hariList[$offset % count($hariList)],
                'jam_mulai' => $jamList[$offset % count($jamList)][0],
                'jam_selesai' => $jamList[$offset % count($jamList)][1],
                'ruang' => $ruangList[$offset % count($ruangList)],
                'kelompok' => $kelompokList[$offset % count($kelompokList)],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($schedules, 50) as $chunk) {
            DB::table('schedules')->insert($chunk);
        }
    }
}
