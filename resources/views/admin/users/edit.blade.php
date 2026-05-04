@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Pengguna</h1>
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Kembali</a>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 max-w-lg">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select name="role" id="role-select"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="mahasiswa" {{ old('role', $user->role) === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="dosen" {{ old('role', $user->role) === 'dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div id="nim-field" class="mb-4 {{ $user->role !== 'mahasiswa' ? 'hidden' : '' }}">
                <label for="nim" class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                <input type="text" name="nim" id="nim" value="{{ old('nim', $user->nim) }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('nim')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div id="nip-field" class="mb-4 {{ $user->role !== 'dosen' ? 'hidden' : '' }}">
                <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                <input type="text" name="nip" id="nip" value="{{ old('nip', $user->nip) }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('nip')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div id="jurusan-field" class="mb-4 {{ !in_array($user->role, ['mahasiswa', 'dosen']) ? 'hidden' : '' }}">
                <label for="jurusan_id" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                <select name="jurusan_id" id="jurusan_id"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach($jurusans as $jurusan)
                    <option value="{{ $jurusan->id }}" {{ old('jurusan_id', $user->jurusan_id) == $jurusan->id ? 'selected' : '' }}>{{ $jurusan->nama }}</option>
                    @endforeach
                </select>
                @error('jurusan_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div id="wali-field" class="mb-4 {{ $user->role !== 'mahasiswa' ? 'hidden' : '' }}">
                <label for="dosen_wali_id" class="block text-sm font-medium text-gray-700 mb-1">Dosen Wali</label>
                <select name="dosen_wali_id" id="dosen_wali_id"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Pilih Dosen Wali --</option>
                    @foreach($dosens as $dosen)
                    <option value="{{ $dosen->id }}" {{ old('dosen_wali_id', $user->dosen_wali_id) == $dosen->id ? 'selected' : '' }}>{{ $dosen->name }}</option>
                    @endforeach
                </select>
                @error('dosen_wali_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span></label>
                <input type="password" name="password" id="password"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Minimal 8 karakter">
                @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Perbarui
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var roleSelect = document.getElementById('role-select');
    var nimField = document.getElementById('nim-field');
    var nipField = document.getElementById('nip-field');
    var jurusanField = document.getElementById('jurusan-field');
    var waliField = document.getElementById('wali-field');

    function toggleFields() {
        var role = roleSelect.value;
        nimField.classList.toggle('hidden', role !== 'mahasiswa');
        nipField.classList.toggle('hidden', role !== 'dosen');
        jurusanField.classList.toggle('hidden', role !== 'mahasiswa' && role !== 'dosen');
        waliField.classList.toggle('hidden', role !== 'mahasiswa');
    }

    roleSelect.addEventListener('change', toggleFields);
    toggleFields();
});
</script>
@endsection
