<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $dosenId = Auth::id();

        $courses = Course::whereHas('lecturers', function ($q) use ($dosenId) {
            $q->where('users.id', $dosenId);
        })->with('jurusan')->orderBy('semester')->orderBy('kode_mk')->get();

        $courseStats = [];
        foreach ($courses as $course) {
            $totalStudents = Enrollment::where('jurusan_id', $course->jurusan_id)
                ->where('semester', $course->semester)
                ->count();

            $totalSessions = $course->attendanceSessions()->count();

            $totalAttendance = Attendance::whereHas('attendanceSession', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })->count();

            $courseStats[$course->id] = [
                'total_students' => $totalStudents,
                'total_sessions' => $totalSessions,
                'total_attendance' => $totalAttendance,
            ];
        }

        return view('dosen.students.index', compact('courses', 'courseStats'));
    }

    public function show(Request $request, Course $course): View
    {
        $dosenId = Auth::id();

        $isOwner = $course->lecturers()->where('users.id', $dosenId)->exists();
        if (!$isOwner) {
            abort(403, 'Anda tidak mengajar mata kuliah ini.');
        }

        $course->load('jurusan', 'schedules');

        $students = Enrollment::where('jurusan_id', $course->jurusan_id)
            ->where('semester', $course->semester)
            ->with('user')
            ->get();

        $totalSessions = $course->attendanceSessions()->count();
        $sessionIds = $course->attendanceSessions()->pluck('id');

        $attendanceStats = [];
        if ($sessionIds->isNotEmpty()) {
            $rawStats = Attendance::whereIn('attendance_session_id', $sessionIds)
                ->select(
                    'user_id',
                    DB::raw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir"),
                    DB::raw("SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin"),
                    DB::raw("SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit"),
                    DB::raw("SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha")
                )
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');
        } else {
            $rawStats = collect();
        }

        $studentData = [];
        foreach ($students as $enrollment) {
            $user = $enrollment->user;
            $stats = $rawStats->get($user->id);

            $hadir = (int)($stats->hadir ?? 0);
            $izin = (int)($stats->izin ?? 0);
            $sakit = (int)($stats->sakit ?? 0);
            $alpha = (int)($stats->alpha ?? 0);
            $totalAbsen = $hadir + $izin + $sakit + $alpha;
            $sisa = $totalSessions - $totalAbsen;

            $studentData[] = [
                'id' => $user->id,
                'nim' => $user->nim ?? '-',
                'name' => $user->name,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'total_absen' => $totalAbsen,
                'sisa' => max(0, $sisa),
                'persentase' => $totalSessions > 0
                    ? round(($hadir + $izin) / $totalSessions * 100, 1)
                    : 0,
            ];
        }

        usort($studentData, fn($a, $b) => strcmp($a['name'], $b['name']));

        return view('dosen.students.show', compact(
            'course', 'studentData', 'totalSessions'
        ));
    }
}
