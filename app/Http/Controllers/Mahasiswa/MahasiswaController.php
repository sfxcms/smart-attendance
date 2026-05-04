<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Schedule;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        $userId = Auth::id();

        $courses = Course::whereExists(function ($query) use ($userId) {
            $query->select(DB::raw(1))
                ->from('enrollments')
                ->whereColumn('courses.jurusan_id', 'enrollments.jurusan_id')
                ->whereColumn('courses.semester', 'enrollments.semester')
                ->where('enrollments.user_id', $userId);
        })->get();

        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $todayName = $days[now()->dayOfWeek];

        $todaySchedules = Schedule::whereExists(function ($query) use ($userId) {
            $query->select(DB::raw(1))
                ->from('courses')
                ->join('enrollments', function ($join) {
                    $join->on('courses.jurusan_id', '=', 'enrollments.jurusan_id')
                         ->on('courses.semester', '=', 'enrollments.semester');
                })
                ->whereColumn('schedules.course_id', 'courses.id')
                ->where('enrollments.user_id', $userId);
        })->where('hari', $todayName)
            ->with(['course.lecturers', 'attendanceSessions' => function ($q) {
                $q->where('status', 'aktif');
            }])
            ->orderBy('jam_mulai')
            ->get();

        $todayActiveSessions = AttendanceSession::whereIn('schedule_id', $todaySchedules->pluck('id'))
            ->where('status', 'aktif')
            ->with(['course', 'schedule'])
            ->get();

        $todayAttendances = Attendance::where('user_id', $userId)
            ->whereHas('attendanceSession', function ($q) {
                $q->whereDate('created_at', today());
            })
            ->with('attendanceSession.course')
            ->get();

        $totalHadir = Attendance::where('user_id', $userId)->where('status', 'hadir')->count();
        $totalIzin = Attendance::where('user_id', $userId)->where('status', 'izin')->count();
        $totalSakit = Attendance::where('user_id', $userId)->where('status', 'sakit')->count();
        $totalAlpha = Attendance::where('user_id', $userId)->where('status', 'alpha')->count();
        $totalAttendance = Attendance::where('user_id', $userId)->count();

        return view('mahasiswa.dashboard', compact(
            'courses',
            'todaySchedules',
            'todayActiveSessions',
            'todayAttendances',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalAlpha',
            'totalAttendance',
        ));
    }
}
