<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::with('lecturers', 'jurusan');

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->input('jurusan_id'));
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->input('semester'));
        }

        $courses = $query->orderBy('semester')->orderBy('kode_mk')->paginate(15)->withQueryString();
        $jurusans = Jurusan::orderBy('nama')->get();
        $selectedJurusan = $request->input('jurusan_id', '');
        $selectedSemester = $request->input('semester', '');

        return view('admin.courses.index', compact('courses', 'jurusans', 'selectedJurusan', 'selectedSemester'));
    }

    public function create(): View
    {
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $jurusans = Jurusan::orderBy('nama')->get();

        return view('admin.courses.create', compact('dosens', 'jurusans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_mk' => 'required|string|max:20|unique:courses,kode_mk',
            'nama_mk' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1',
            'jurusan_id' => 'nullable|exists:jurusans,id',
        ]);

        $course = Course::create($validated);

        if ($request->has('lecturers')) {
            $course->lecturers()->attach($request->input('lecturers', []));
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course berhasil ditambahkan.');
    }

    public function edit($id): View
    {
        $course = Course::with('lecturers')->findOrFail($id);
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();
        $jurusans = Jurusan::orderBy('nama')->get();
        $assignedLecturerIds = $course->lecturers->pluck('id')->toArray();

        return view('admin.courses.edit', compact('course', 'dosens', 'jurusans', 'assignedLecturerIds'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'kode_mk' => 'required|string|max:20|unique:courses,kode_mk,' . $id,
            'nama_mk' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1',
            'jurusan_id' => 'nullable|exists:jurusans,id',
        ]);

        $course->update($validated);

        $course->lecturers()->sync($request->input('lecturers', []));

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course berhasil diperbarui.');
    }

    public function destroy($id): RedirectResponse
    {
        $course = Course::findOrFail($id);
        $course->lecturers()->detach();
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course berhasil dihapus.');
    }
}
