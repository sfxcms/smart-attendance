<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with(['jurusan', 'dosenWali']);

        if ($request->filled('role') && $request->input('role') !== 'semua') {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->input('jurusan_id'));
        }

        $users = $query->orderBy('name')->paginate(20)->withQueryString();
        $selectedRole = $request->input('role', 'semua');
        $selectedJurusan = $request->input('jurusan_id', '');
        $jurusans = Jurusan::orderBy('nama')->get();

        return view('admin.users.index', compact('users', 'selectedRole', 'selectedJurusan', 'jurusans'));
    }

    public function create(): View
    {
        $jurusans = Jurusan::orderBy('nama')->get();
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();

        return view('admin.users.create', compact('jurusans', 'dosens'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,dosen,mahasiswa',
            'nim' => 'nullable|string|max:20|unique:users,nim',
            'nip' => 'nullable|string|max:20|unique:users,nip',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'dosen_wali_id' => 'nullable|exists:users,id',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id): View
    {
        $user = User::findOrFail($id);
        $jurusans = Jurusan::orderBy('nama')->get();
        $dosens = User::where('role', 'dosen')->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'jurusans', 'dosens'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,dosen,mahasiswa',
            'nim' => 'nullable|string|max:20|unique:users,nim,' . $id,
            'nip' => 'nullable|string|max:20|unique:users,nip,' . $id,
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'dosen_wali_id' => 'nullable|exists:users,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
