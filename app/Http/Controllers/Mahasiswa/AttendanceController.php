<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function showScan()
    {
        $userId = Auth::id();

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

        $todayAttendances = Attendance::where('user_id', $userId)
            ->whereHas('attendanceSession.schedule', function ($q) use ($todayName) {
                $q->where('hari', $todayName);
            })
            ->with('attendanceSession.course')
            ->get();

        return view('mahasiswa.attendance.scan', compact('todaySchedules', 'todayAttendances'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        $userId = Auth::id();
        $qrData = $request->input('qr_data');

        $sessionId = $this->parseSessionId($qrData);

        if (!$sessionId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data QR tidak valid.',
                ], 400);
            }
            return redirect()->back()->with('error', 'Data QR tidak valid.');
        }

        $session = AttendanceSession::with(['course', 'schedule'])->find($sessionId);

        if (!$session) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi absensi tidak ditemukan.',
                ], 404);
            }
            return redirect()->back()->with('error', 'Sesi absensi tidak ditemukan.');
        }

        if ($session->status !== 'aktif') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi absensi sudah ditutup.',
                ], 400);
            }
            return redirect()->back()->with('error', 'Sesi absensi sudah ditutup.');
        }

        if ($session->expires_at && now()->greaterThan($session->expires_at)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi absensi sudah kadaluwarsa.',
                ], 400);
            }
            return redirect()->back()->with('error', 'Sesi absensi sudah kadaluwarsa.');
        }

        $isEnrolled = Enrollment::where('user_id', $userId)
            ->where('jurusan_id', $session->course->jurusan_id)
            ->where('semester', $session->course->semester)
            ->exists();

        if (!$isEnrolled) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak terdaftar di mata kuliah ini.',
                ], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak terdaftar di mata kuliah ini.');
        }

        $alreadyAttended = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyAttended) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absensi pada sesi ini.',
                ], 400);
            }
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi pada sesi ini.');
        }

        Attendance::create([
            'attendance_session_id' => $session->id,
            'user_id' => $userId,
            'status' => 'hadir',
            'scanned_at' => now(),
        ]);

        $session->increment('total_mahasiswa');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil!',
                'data' => [
                    'course' => $session->course->nama_mk ?? 'Mata Kuliah',
                    'time' => $session->schedule
                        ? $session->schedule->jam_mulai . ' - ' . $session->schedule->jam_selesai
                        : '-',
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Absensi berhasil untuk ' . ($session->course->nama_mk ?? 'Mata Kuliah') . '!');
    }

    public function history(Request $request)
    {
        $userId = Auth::id();

        $courses = Course::whereExists(function ($query) use ($userId) {
            $query->select(DB::raw(1))
                ->from('enrollments')
                ->whereColumn('courses.jurusan_id', 'enrollments.jurusan_id')
                ->whereColumn('courses.semester', 'enrollments.semester')
                ->where('enrollments.user_id', $userId);
        })->get();

        $query = Attendance::where('user_id', $userId)
            ->with(['attendanceSession.course', 'attendanceSession.schedule']);

        if ($request->filled('course_id')) {
            $query->whereHas('attendanceSession', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('month')) {
            $query->whereHas('attendanceSession', function ($q) use ($request) {
                $q->whereMonth('created_at', $request->month);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('scanned_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $totalHadir = Attendance::where('user_id', $userId)->where('status', 'hadir')->count();
        $totalIzin = Attendance::where('user_id', $userId)->where('status', 'izin')->count();
        $totalSakit = Attendance::where('user_id', $userId)->where('status', 'sakit')->count();
        $totalAlpha = Attendance::where('user_id', $userId)->where('status', 'alpha')->count();
        $totalAttendance = Attendance::where('user_id', $userId)->count();

        return view('mahasiswa.attendance.history', compact(
            'attendances',
            'courses',
            'totalHadir',
            'totalIzin',
            'totalSakit',
            'totalAlpha',
            'totalAttendance',
        ));
    }

    private function parseSessionId($qrData)
    {
        if (is_numeric($qrData)) {
            return (int) $qrData;
        }

        $patterns = [
            '/[?&]session[=_]?id[=:](\d+)/i',
            '/\/attendance-sessions?\/(\d+)/i',
            '/\/sessions?\/(\d+)/i',
            '/[?&]id[=:](\d+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $qrData, $matches)) {
                return (int) $matches[1];
            }
        }

        $urlPath = parse_url($qrData, PHP_URL_PATH);
        if ($urlPath) {
            $segments = explode('/', trim($urlPath, '/'));
            $lastSegment = end($segments);
            if (is_numeric($lastSegment)) {
                return (int) $lastSegment;
            }
        }

        return null;
    }
}
