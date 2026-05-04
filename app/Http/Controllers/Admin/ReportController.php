<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::orderBy('nama_mk')->get();

        $query = Attendance::with(['user', 'attendanceSession.course', 'attendanceSession']);

        if ($request->filled('course_id')) {
            $query->whereHas('attendanceSession', function ($q) use ($request) {
                $q->where('course_id', $request->input('course_id'));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereHas('attendanceSession', function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->input('date_from'));
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('attendanceSession', function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->input('date_to'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $attendances = $query->orderByDesc('scanned_at')->paginate(25)->withQueryString();

        return view('admin.reports.index', compact('attendances', 'courses'));
    }
}
