<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JurusanController extends Controller
{
    public function index(): View
    {
        $jurusans = Jurusan::orderBy('nama')->paginate(15);

        return view('admin.jurusans.index', compact('jurusans'));
    }

    public function create(): View
    {
        return view('admin.jurusans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:10|unique:jurusans,kode',
        ]);

        Jurusan::create($validated);

        return redirect()->route('admin.jurusans.index')
            ->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit($id): View
    {
        $jurusan = Jurusan::findOrFail($id);

        return view('admin.jurusans.edit', compact('jurusan'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $jurusan = Jurusan::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:10|unique:jurusans,kode,' . $id,
        ]);

        $jurusan->update($validated);

        return redirect()->route('admin.jurusans.index')
            ->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy($id): RedirectResponse
    {
        $jurusan = Jurusan::findOrFail($id);

        if ($jurusan->users()->exists() || $jurusan->courses()->exists()) {
            return redirect()->route('admin.jurusans.index')
                ->with('error', 'Jurusan tidak dapat dihapus karena masih memiliki data terkait.');
        }

        $jurusan->delete();

        return redirect()->route('admin.jurusans.index')
            ->with('success', 'Jurusan berhasil dihapus.');
    }
}
