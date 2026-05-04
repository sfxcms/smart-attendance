<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateSessionRequest;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DosenController extends Controller
{
    /**
     * List attendance sessions opened by the authenticated dosen.
     */
    public function sessions(Request $request): JsonResponse
    {
        $dosenId = Auth::id();

        $query = AttendanceSession::where('dosen_id', $dosenId)
            ->with(['course', 'schedule']);

        if ($request->filled('status') && in_array($request->status, ['aktif', 'ditutup'])) {
            $query->where('status', $request->status);
        }

        $sessions = $query->latest()->paginate(15);

        $transformed = $sessions->map(function ($session) {
            return [
                'id' => $session->id,
                'course_name' => $session->course->nama_mk ?? '-',
                'course_code' => $session->course->kode_mk ?? '-',
                'schedule' => $session->schedule
                    ? [
                        'hari' => $session->schedule->hari,
                        'jam_mulai' => $session->schedule->jam_mulai,
                        'jam_selesai' => $session->schedule->jam_selesai,
                        'ruang' => $session->schedule->ruang,
                    ]
                    : null,
                'status' => $session->status,
                'tipe_sesi' => $session->tipe_sesi,
                'link_meeting' => $session->link_meeting,
                'expires_at' => $session->expires_at ? $session->expires_at->toDateTimeString() : null,
                'total_mahasiswa' => $session->total_mahasiswa,
                'total_attended' => $session->attendances()->count(),
                'created_at' => $session->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformed,
            'pagination' => [
                'current_page' => $sessions->currentPage(),
                'last_page' => $sessions->lastPage(),
                'per_page' => $sessions->perPage(),
                'total' => $sessions->total(),
            ],
        ]);
    }

    /**
     * Create a new attendance session.
     */
    public function createSession(CreateSessionRequest $request): JsonResponse
    {
        $dosenId = Auth::id();
        $schedule = Schedule::with('course')->findOrFail($request->input('schedule_id'));

        $isOwner = $schedule->course->lecturers()->where('users.id', $dosenId)->exists();
        if (!$isOwner) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak mengajar mata kuliah ini.',
            ], 403);
        }

        $qrToken = 'qr_' . Str::random(32) . '_' . time();

        $session = AttendanceSession::create([
            'schedule_id' => $schedule->id,
            'course_id' => $schedule->course_id,
            'dosen_id' => $dosenId,
            'status' => 'aktif',
            'tipe_sesi' => $request->input('tipe_sesi', 'offline'),
            'link_meeting' => $request->input('link_meeting'),
            'expires_at' => now()->addMinutes(15),
            'qr_code' => $qrToken,
            'total_mahasiswa' => Enrollment::where('jurusan_id', $schedule->course->jurusan_id)
                ->where('semester', $schedule->course->semester)
                ->count(),
        ]);

        $qrContent = config('app.url') . '/attendance/scan/' . $session->id . '?token=' . $qrToken;

        return response()->json([
            'success' => true,
            'message' => 'Sesi absensi berhasil dibuat.',
            'data' => [
                'id' => $session->id,
                'course_name' => $schedule->course->nama_mk,
                'tipe_sesi' => $session->tipe_sesi,
                'link_meeting' => $session->link_meeting,
                'expires_at' => $session->expires_at->toDateTimeString(),
                'qr_content' => $qrContent,
                'qr_token' => $qrToken,
            ],
        ], 201);
    }

    /**
     * Get attendance records for a specific session (owner only).
     */
    public function sessionAttendance(int $sessionId): JsonResponse
    {
        $dosenId = Auth::id();
        $session = AttendanceSession::with(['course', 'schedule'])->find($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi absensi tidak ditemukan.',
            ], 404);
        }

        if ($session->dosen_id !== $dosenId) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke sesi ini.',
            ], 403);
        }

        $attendances = Attendance::where('attendance_session_id', $session->id)
            ->with('user')
            ->orderBy('scanned_at')
            ->get()
            ->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'student_name' => $attendance->user->name ?? '-',
                    'student_nim' => $attendance->user->nim ?? '-',
                    'status' => $attendance->status,
                    'scanned_at' => $attendance->scanned_at ? $attendance->scanned_at->toDateTimeString() : null,
                ];
            });

        $stats = [
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'alpha' => $attendances->where('status', 'alpha')->count(),
            'belum' => $session->total_mahasiswa - $attendances->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'session' => [
                    'id' => $session->id,
                    'course_name' => $session->course->nama_mk ?? '-',
                    'course_code' => $session->course->kode_mk ?? '-',
                    'status' => $session->status,
                    'tipe_sesi' => $session->tipe_sesi,
                    'link_meeting' => $session->link_meeting,
                    'expires_at' => $session->expires_at ? $session->expires_at->toDateTimeString() : null,
                    'total_mahasiswa' => $session->total_mahasiswa,
                ],
                'attendances' => $attendances,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Close an active attendance session (owner only).
     */
    public function closeSession(int $sessionId): JsonResponse
    {
        $dosenId = Auth::id();
        $session = AttendanceSession::find($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi absensi tidak ditemukan.',
            ], 404);
        }

        if ($session->dosen_id !== $dosenId) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke sesi ini.',
            ], 403);
        }

        if ($session->status === 'ditutup') {
            return response()->json([
                'success' => false,
                'message' => 'Sesi sudah ditutup sebelumnya.',
            ], 400);
        }

        $session->update(['status' => 'ditutup']);

        return response()->json([
            'success' => true,
            'message' => 'Sesi absensi berhasil ditutup.',
        ]);
    }

    /**
     * List courses taught by the authenticated dosen (for create session form).
     */
    public function myCourses(): JsonResponse
    {
        $dosenId = Auth::id();
        $courses = Course::whereHas('lecturers', function ($q) use ($dosenId) {
            $q->where('users.id', $dosenId);
        })->with('jurusan')->get()->map(function ($course) {
            return [
                'id' => $course->id,
                'nama' => $course->nama_mk,
                'kode_mk' => $course->kode_mk,
                'semester' => $course->semester,
                'jurusan' => $course->jurusan->nama ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $courses,
        ]);
    }

    /**
     * Get schedules for a specific course taught by the dosen.
     */
    public function courseSchedules(int $courseId): JsonResponse
    {
        $dosenId = Auth::id();

        $isOwner = Course::whereHas('lecturers', function ($q) use ($dosenId) {
            $q->where('users.id', $dosenId);
        })->where('id', $courseId)->exists();

        if (!$isOwner) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak mengajar mata kuliah ini.',
            ], 403);
        }

        $schedules = Schedule::where('course_id', $courseId)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'hari' => $schedule->hari,
                    'jam_mulai' => $schedule->jam_mulai,
                    'jam_selesai' => $schedule->jam_selesai,
                    'ruang' => $schedule->ruang,
                    'kelompok' => $schedule->kelompok,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }
}
