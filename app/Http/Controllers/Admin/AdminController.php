<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Schedule;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalCourses = Course::count();
        $totalDosen = User::where('role', 'dosen')->count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalSchedules = Schedule::count();
        $activeSessions = AttendanceSession::where('status', 'aktif')->count();
        $totalAttendance = Attendance::count();

        $recentSessions = AttendanceSession::with(['course', 'dosen', 'schedule'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalCourses', 'totalDosen', 'totalMahasiswa', 'totalSchedules',
            'activeSessions', 'totalAttendance', 'recentSessions'
        ));
    }
}
