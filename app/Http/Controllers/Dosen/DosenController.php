<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\AttendanceSession;
use Illuminate\Support\Facades\Auth;

class DosenController extends Controller
{
    public function dashboard()
    {
        $dosenId = Auth::id();

        $courses = Auth::user()->coursesTaught()->with(['schedules', 'lecturers'])->get();

        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $todayName = $days[now()->dayOfWeek];

        $todaySchedules = Schedule::whereHas('course.lecturers', function ($q) use ($dosenId) {
            $q->where('users.id', $dosenId);
        })->where('hari', $todayName)->with('course')->orderBy('jam_mulai')->get();

        $activeSessions = AttendanceSession::where('dosen_id', $dosenId)
            ->where('status', 'aktif')
            ->with(['course', 'schedule'])
            ->get();

        $recentSessions = AttendanceSession::where('dosen_id', $dosenId)
            ->withCount('attendances')
            ->latest()
            ->take(10)
            ->with(['course', 'schedule'])
            ->get();

        return view('dosen.dashboard', compact('courses', 'todaySchedules', 'activeSessions', 'recentSessions'));
    }
}
