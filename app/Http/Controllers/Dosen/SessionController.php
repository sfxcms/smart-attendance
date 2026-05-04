<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\Enrollment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $dosenId = Auth::id();

        $query = AttendanceSession::where('dosen_id', $dosenId)
            ->with(['course', 'schedule']);

        if ($request->filled('status') && in_array($request->status, ['aktif', 'ditutup'])) {
            $query->where('status', $request->status);
        }

        $sessions = $query->withCount('attendances')->latest()->paginate(15);

        return view('dosen.sessions.index', compact('sessions'));
    }

    public function create(Schedule $schedule = null)
    {
        $dosenId = Auth::id();

        $schedules = Schedule::whereHas('course.lecturers', function ($q) use ($dosenId) {
            $q->where('users.id', $dosenId);
        })->with('course')->orderBy('jam_mulai')->get();

        return view('dosen.sessions.create', compact('schedules', 'schedule'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'schedule_id'  => 'required|exists:schedules,id',
            'tipe_sesi'    => 'required|in:online,offline',
            'link_meeting' => 'required_if:tipe_sesi,online|nullable|url|max:500',
        ]);

        $schedule = Schedule::with('course')->findOrFail($validated['schedule_id']);

        $dosenId = Auth::id();
        $isOwner = $schedule->course->lecturers()->where('users.id', $dosenId)->exists();

        if (! $isOwner) {
            abort(403, 'Anda tidak mengajar mata kuliah ini.');
        }

        $qrToken = 'qr_' . Str::random(32) . '_' . time();

        $session = AttendanceSession::create([
            'schedule_id' => $validated['schedule_id'],
            'course_id'   => $schedule->course_id,
            'dosen_id'    => $dosenId,
            'status'      => 'aktif',
            'tipe_sesi'   => $validated['tipe_sesi'],
            'link_meeting' => $validated['link_meeting'] ?? null,
            'expires_at'  => now()->addMinutes(15),
            'qr_code'     => $qrToken,
            'total_mahasiswa' => Enrollment::where('jurusan_id', $schedule->course->jurusan_id)
                ->where('semester', $schedule->course->semester)
                ->count(),
        ]);

        return redirect()
            ->route('dosen.sessions.show', $session)
            ->with('success', 'Sesi absensi berhasil dibuka.');
    }

    public function show(AttendanceSession $session)
    {
        if ($session->dosen_id !== Auth::id()) {
            abort(403);
        }

        $session->load([
            'course',
            'schedule',
            'dosen',
            'attendances.user' => function ($q) {
                $q->select('id', 'name', 'nim');
            },
        ]);

        $qrContent = config('app.url') . '/dosen/sessions/' . $session->id;
        $qrSvg = QrCode::format('svg')->size(300)->generate($qrContent);
        $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        $stats = [
            'hadir'  => $session->attendances->where('status', 'hadir')->count(),
            'izin'   => $session->attendances->where('status', 'izin')->count(),
            'sakit'  => $session->attendances->where('status', 'sakit')->count(),
            'alpha'  => $session->attendances->where('status', 'alpha')->count(),
            'belum'  => $session->total_mahasiswa - $session->attendances->count(),
        ];

        return view('dosen.sessions.show', compact('session', 'qrDataUri', 'stats'));
    }

    public function close(AttendanceSession $session)
    {
        if ($session->dosen_id !== Auth::id()) {
            abort(403);
        }

        $session->update(['status' => 'ditutup']);

        return back()->with('success', 'Sesi absensi telah ditutup.');
    }
}
