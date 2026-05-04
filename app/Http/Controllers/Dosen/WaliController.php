<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WaliController extends Controller
{
    public function index(): View
    {
        $dosenId = Auth::id();

        $students = User::where('dosen_wali_id', $dosenId)
            ->with(['jurusan'])
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $attendances = Attendance::where('user_id', $student->id)->get();
                $student->total_attendances = $attendances->count();
                $student->hadir_count = $attendances->where('status', 'hadir')->count();
                $student->izin_count = $attendances->where('status', 'izin')->count();
                $student->sakit_count = $attendances->where('status', 'sakit')->count();
                $student->alpha_count = $attendances->where('status', 'alpha')->count();
                return $student;
            });

        return view('dosen.wali.index', compact('students'));
    }

    public function show($id): View
    {
        $dosenId = Auth::id();

        $student = User::where('id', $id)
            ->where('dosen_wali_id', $dosenId)
            ->with(['jurusan', 'dosenWali'])
            ->firstOrFail();

        $attendances = Attendance::where('user_id', $student->id)
            ->with(['attendanceSession.course', 'attendanceSession.schedule'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('dosen.wali.show', compact('student', 'attendances'));
    }
}
