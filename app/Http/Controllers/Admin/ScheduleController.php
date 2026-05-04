<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Jurusan;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $hariOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $query = Schedule::with('course.jurusan');

        if ($request->filled('jurusan_id')) {
            $query->whereHas('course', function ($q) use ($request) {
                $q->where('jurusan_id', $request->input('jurusan_id'));
            });
        }

        $schedules = $query->get();

        $groupedSchedules = $schedules->sortBy(function ($schedule) use ($hariOrder) {
            return array_search($schedule->hari, $hariOrder);
        })->groupBy('hari');

        $jurusans = Jurusan::orderBy('nama')->get();
        $selectedJurusan = $request->input('jurusan_id', '');

        return view('admin.schedules.index', compact('groupedSchedules', 'hariOrder', 'jurusans', 'selectedJurusan'));
    }

    public function create(): View
    {
        $courses = Course::with('jurusan')->orderBy('nama_mk')->get();

        return view('admin.schedules.create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang' => 'required|string|max:50',
            'kelompok' => 'nullable|string|max:20',
        ]);

        Schedule::create($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit($id): View
    {
        $schedule = Schedule::findOrFail($id);
        $courses = Course::with('jurusan')->orderBy('nama_mk')->get();

        return view('admin.schedules.edit', compact('schedule', 'courses'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $schedule = Schedule::findOrFail($id);

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruang' => 'required|string|max:50',
            'kelompok' => 'nullable|string|max:20',
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy($id): RedirectResponse
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}
