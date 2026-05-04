<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Enrollment::with(['user', 'jurusan']);

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->input('jurusan_id'));
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->input('semester'));
        }

        $enrollments = $query->orderBy('semester')
            ->orderBy('jurusan_id')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $jurusans = Jurusan::orderBy('nama')->get();
        $semesters = range(1, 8);
        $selectedJurusan = $request->input('jurusan_id', '');
        $selectedSemester = $request->input('semester', '');

        return view('admin.mahasiswa.index', compact(
            'enrollments', 'jurusans', 'semesters',
            'selectedJurusan', 'selectedSemester'
        ));
    }
}
