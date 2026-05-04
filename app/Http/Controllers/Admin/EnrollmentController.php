<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Enrollment::with(['user.jurusan', 'jurusan']);

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->input('jurusan_id'));
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->input('semester'));
        }

        $enrollments = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $jurusans = Jurusan::orderBy('nama')->get();
        $semesters = range(1, 8);
        $selectedJurusan = $request->input('jurusan_id', '');
        $selectedSemester = $request->input('semester', '');

        return view('admin.enrollments.index', compact('enrollments', 'jurusans', 'semesters', 'selectedJurusan', 'selectedSemester'));
    }

    public function create(): View
    {
        $mahasiswas = User::where('role', 'mahasiswa')->orderBy('name')->get();
        $jurusans = Jurusan::orderBy('nama')->get();
        $semesters = range(1, 8);

        return view('admin.enrollments.create', compact('mahasiswas', 'jurusans', 'semesters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'jurusan_id' => 'required|exists:jurusans,id',
            'semester' => 'required|integer|between:1,14',
        ]);

        $exists = Enrollment::where('user_id', $validated['user_id'])
            ->where('jurusan_id', $validated['jurusan_id'])
            ->where('semester', $validated['semester'])
            ->exists();

        if ($exists) {
            return back()->withInput()
                ->with('error', 'Mahasiswa sudah terdaftar di jurusan dan semester ini.');
        }

        Enrollment::create($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Pendaftaran berhasil ditambahkan.');
    }

    public function destroy($id): RedirectResponse
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Pendaftaran berhasil dihapus.');
    }
}
