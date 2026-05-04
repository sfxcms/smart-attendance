<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        $totalCourses = Course::count();
        $totalDosen = User::where('role', 'dosen')->count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalSchedules = \App\Models\Schedule::count();
        $activeSessions = AttendanceSession::where('status', 'aktif')->count();
        $totalAttendance = Attendance::count();

        $recentSessions = AttendanceSession::with(['course', 'dosen', 'schedule'])
            ->latest()
            ->take(10)
            ->get();

        $byJurusan = $this->byJurusan();
        $bySemester = $this->bySemester();
        $byCourse = $this->byCourse();
        $trend = $this->trend();

        return view('admin.dashboard', compact(
            'totalCourses', 'totalDosen', 'totalMahasiswa', 'totalSchedules',
            'activeSessions', 'totalAttendance', 'recentSessions',
            'byJurusan', 'bySemester', 'byCourse', 'trend'
        ));
    }

    public function index()
    {
        $overview = $this->overview();

        $byJurusan = $this->byJurusan();
        $bySemester = $this->bySemester();
        $byCourse = $this->byCourse();
        $trend = $this->trend();

        $jurusans = Jurusan::all();

        return view('admin.analytics.index', compact(
            'overview', 'byJurusan', 'bySemester', 'byCourse', 'trend', 'jurusans'
        ));
    }

    private function overview(): array
    {
        $totalAttendance = Attendance::count();
        $totalHadir = Attendance::where('status', 'hadir')->count();
        $totalIzin = Attendance::where('status', 'izin')->count();
        $totalSakit = Attendance::where('status', 'sakit')->count();
        $totalAlpha = Attendance::where('status', 'alpha')->count();
        $totalMhs = User::where('role', 'mahasiswa')->count();
        $mhsWithAttendance = Attendance::distinct('user_id')->count('user_id');

        return [
            'total_attendance' => $totalAttendance,
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_alpha' => $totalAlpha,
            'attendance_rate' => $totalAttendance > 0
                ? round(($totalHadir + $totalIzin) / $totalAttendance * 100, 1)
                : 0,
            'total_mahasiswa' => $totalMhs,
            'mhs_with_attendance' => $mhsWithAttendance,
        ];
    }

    private function byJurusan(): array
    {
        $data = DB::table('attendances')
            ->join('attendance_sessions', 'attendances.attendance_session_id', '=', 'attendance_sessions.id')
            ->join('courses', 'attendance_sessions.course_id', '=', 'courses.id')
            ->join('jurusans', 'courses.jurusan_id', '=', 'jurusans.id')
            ->select(
                'jurusans.nama',
                'jurusans.kode',
                DB::raw('COUNT(attendances.id) as total'),
                DB::raw("SUM(CASE WHEN attendances.status = 'hadir' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN attendances.status = 'izin' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN attendances.status = 'sakit' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN attendances.status = 'alpha' THEN 1 ELSE 0 END) as alpha")
            )
            ->groupBy('jurusans.id', 'jurusans.nama', 'jurusans.kode')
            ->get();

        $result = ['labels' => [], 'rates' => [], 'hadir' => [], 'izin' => [], 'sakit' => [], 'alpha' => []];
        foreach ($data as $row) {
            $result['labels'][] = $row->nama;
            $rate = $row->total > 0 ? round(($row->hadir + $row->izin) / $row->total * 100, 1) : 0;
            $result['rates'][] = $rate;
            $result['hadir'][] = (int) $row->hadir;
            $result['izin'][] = (int) $row->izin;
            $result['sakit'][] = (int) $row->sakit;
            $result['alpha'][] = (int) $row->alpha;
        }

        return $result;
    }

    private function bySemester(): array
    {
        $data = DB::table('attendances')
            ->join('attendance_sessions', 'attendances.attendance_session_id', '=', 'attendance_sessions.id')
            ->join('courses', 'attendance_sessions.course_id', '=', 'courses.id')
            ->select(
                'courses.semester',
                DB::raw('COUNT(attendances.id) as total'),
                DB::raw("SUM(CASE WHEN attendances.status = 'hadir' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN attendances.status = 'izin' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN attendances.status = 'sakit' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN attendances.status = 'alpha' THEN 1 ELSE 0 END) as alpha")
            )
            ->groupBy('courses.semester')
            ->orderBy('courses.semester')
            ->get();

        $result = ['labels' => [], 'rates' => [], 'hadir' => [], 'izin' => [], 'sakit' => [], 'alpha' => []];
        foreach ($data as $row) {
            $result['labels'][] = 'Semester ' . $row->semester;
            $rate = $row->total > 0 ? round(($row->hadir + $row->izin) / $row->total * 100, 1) : 0;
            $result['rates'][] = $rate;
            $result['hadir'][] = (int) $row->hadir;
            $result['izin'][] = (int) $row->izin;
            $result['sakit'][] = (int) $row->sakit;
            $result['alpha'][] = (int) $row->alpha;
        }

        return $result;
    }

    private function byCourse(): array
    {
        $data = DB::table('attendances')
            ->join('attendance_sessions', 'attendances.attendance_session_id', '=', 'attendance_sessions.id')
            ->join('courses', 'attendance_sessions.course_id', '=', 'courses.id')
            ->select(
                'courses.nama_mk',
                'courses.kode_mk',
                DB::raw('COUNT(attendances.id) as total'),
                DB::raw("SUM(CASE WHEN attendances.status = 'hadir' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN attendances.status = 'izin' THEN 1 ELSE 0 END) as izin")
            )
            ->groupBy('courses.id', 'courses.nama_mk', 'courses.kode_mk')
            ->havingRaw('COUNT(attendances.id) > 0')
            ->orderByRaw('COUNT(attendances.id) DESC')
            ->limit(20)
            ->get();

        $result = ['labels' => [], 'rates' => [], 'total' => []];
        foreach ($data as $row) {
            $label = substr($row->kode_mk . ' - ' . $row->nama_mk, 0, 30);
            $result['labels'][] = $label;
            $rate = $row->total > 0 ? round(($row->hadir + $row->izin) / $row->total * 100, 1) : 0;
            $result['rates'][] = $rate;
            $result['total'][] = (int) $row->total;
        }

        return $result;
    }

    private function trend(): array
    {
        $data = DB::table('attendances')
            ->join('attendance_sessions', 'attendances.attendance_session_id', '=', 'attendance_sessions.id')
            ->select(
                DB::raw("DATE(attendances.scanned_at) as date"),
                DB::raw('COUNT(attendances.id) as total'),
                DB::raw("SUM(CASE WHEN attendances.status = 'hadir' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN attendances.status = 'izin' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN attendances.status = 'sakit' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN attendances.status = 'alpha' THEN 1 ELSE 0 END) as alpha")
            )
            ->where('attendances.scanned_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw("DATE(attendances.scanned_at)"))
            ->orderByRaw('DATE(attendances.scanned_at) ASC')
            ->get();

        $result = ['labels' => [], 'hadir' => [], 'izin' => [], 'sakit' => [], 'alpha' => []];
        foreach ($data as $row) {
            $result['labels'][] = $row->date;
            $result['hadir'][] = (int) $row->hadir;
            $result['izin'][] = (int) $row->izin;
            $result['sakit'][] = (int) $row->sakit;
            $result['alpha'][] = (int) $row->alpha;
        }

        return $result;
    }
}
