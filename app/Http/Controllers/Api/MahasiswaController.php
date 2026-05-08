<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ScanQrRequest;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\Schedule;
use App\Services\QRCodeService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    public function __construct(private readonly QRCodeService $qrCodeService) {}

    /**
     * Get today's enrolled schedules for the authenticated mahasiswa.
     */
    public function schedules(): JsonResponse
    {
        $userId = Auth::id();
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $todayName = $days[now()->dayOfWeek];

        $schedules = Schedule::whereExists(function ($query) use ($userId) {
            $query->select(DB::raw(1))
                ->from('courses')
                ->join('enrollments', function ($join) {
                    $join->on('courses.jurusan_id', '=', 'enrollments.jurusan_id')
                        ->on('courses.semester', '=', 'enrollments.semester');
                })
                ->whereColumn('schedules.course_id', 'courses.id')
                ->where('enrollments.user_id', $userId);
        })
            ->where('hari', $todayName)
            ->with(['course' => function ($q) {
                $q->with('jurusan');
            }, 'attendanceSessions' => function ($q) {
                $q->where('status', 'aktif');
            }])
            ->orderBy('jam_mulai')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'course_name' => $schedule->course->nama_mk ?? '-',
                    'course_code' => $schedule->course->kode_mk ?? '-',
                    'hari' => $schedule->hari,
                    'jam_mulai' => $schedule->jam_mulai,
                    'jam_selesai' => $schedule->jam_selesai,
                    'ruang' => $schedule->ruang,
                    'kelompok' => $schedule->kelompok,
                    'jurusan' => $schedule->course->jurusan->nama ?? '-',
                    'has_active_session' => $schedule->attendanceSessions->isNotEmpty(),
                    'active_session_id' => $schedule->attendanceSessions->first()->id ?? null,
                    'tipe_sesi' => $schedule->attendanceSessions->first()->tipe_sesi ?? null,
                    'link_meeting' => $schedule->attendanceSessions->first()->link_meeting ?? null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    /**
     * Scan QR code and record attendance.
     */
    public function scan(ScanQrRequest $request): JsonResponse
    {
        $userId = Auth::id();
        $sessionId = $this->parseSessionId($request->input('qr_data'));

        if (! $sessionId) {
            return response()->json([
                'success' => false,
                'message' => 'Data QR tidak valid.',
            ], 400);
        }

        $session = AttendanceSession::with(['course', 'schedule'])->find($sessionId);

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi absensi tidak ditemukan.',
            ], 404);
        }

        if (! $this->qrCodeService->verifyQrCode($request->input('qr_data'), $session)) {
            return response()->json([
                'success' => false,
                'message' => 'Token QR tidak valid.',
            ], 400);
        }

        if ($session->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Sesi absensi sudah ditutup.',
            ], 400);
        }

        if ($session->expires_at && now()->greaterThan($session->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi absensi sudah kadaluwarsa.',
            ], 400);
        }

        $isEnrolled = Enrollment::where('user_id', $userId)
            ->where('jurusan_id', $session->course->jurusan_id)
            ->where('semester', $session->course->semester)
            ->exists();

        if (! $isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar di mata kuliah ini.',
            ], 403);
        }

        $alreadyAttended = Attendance::where('attendance_session_id', $session->id)
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyAttended) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi pada sesi ini.',
            ], 409);
        }

        try {
            Attendance::create([
                'attendance_session_id' => $session->id,
                'user_id' => $userId,
                'status' => 'hadir',
                'scanned_at' => now(),
            ]);
        } catch (QueryException $exception) {
            if ($this->isDuplicateAttendanceConstraintViolation($exception)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absensi pada sesi ini.',
                ], 409);
            }

            throw $exception;
        }

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil!',
            'data' => [
                'course_name' => $session->course->nama_mk ?? 'Mata Kuliah',
                'course_code' => $session->course->kode_mk ?? '-',
                'time' => $session->schedule
                    ? $session->schedule->jam_mulai.' - '.$session->schedule->jam_selesai
                    : '-',
                'room' => $session->schedule->ruang ?? '-',
                'tipe_sesi' => $session->tipe_sesi,
                'scanned_at' => now()->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Get paginated attendance history for the authenticated mahasiswa.
     */
    public function history(Request $request): JsonResponse
    {
        $userId = Auth::id();

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
            ->paginate(15);

        $transformed = $attendances->map(function ($attendance) {
            return [
                'id' => $attendance->id,
                'course_name' => $attendance->attendanceSession->course->nama_mk ?? '-',
                'course_code' => $attendance->attendanceSession->course->kode_mk ?? '-',
                'date' => $attendance->scanned_at ? $attendance->scanned_at->format('Y-m-d') : '-',
                'time' => $attendance->attendanceSession->schedule
                    ? $attendance->attendanceSession->schedule->jam_mulai.' - '.$attendance->attendanceSession->schedule->jam_selesai
                    : '-',
                'status' => $attendance->status,
            ];
        });

        $stats = [
            'hadir' => Attendance::where('user_id', $userId)->where('status', 'hadir')->count(),
            'izin' => Attendance::where('user_id', $userId)->where('status', 'izin')->count(),
            'sakit' => Attendance::where('user_id', $userId)->where('status', 'sakit')->count(),
            'alpha' => Attendance::where('user_id', $userId)->where('status', 'alpha')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $transformed,
            'pagination' => [
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'per_page' => $attendances->perPage(),
                'total' => $attendances->total(),
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Parse session ID from QR data string.
     */
    private function parseSessionId(string $qrData): ?int
    {
        if (is_numeric($qrData)) {
            return (int) $qrData;
        }

        $patterns = [
            '/[?&]session[=_]?id[=:](\d+)/i',
            '/\/attendance-sessions?\/(\d+)/i',
            '/\/sessions?\/(\d+)/i',
            '/[?&]id[=:](\d+)/i',
            '/\/attendance\/scan\/(\d+)/i',
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

    private function isDuplicateAttendanceConstraintViolation(QueryException $exception): bool
    {
        return $exception->getCode() === '23000';
    }
}
