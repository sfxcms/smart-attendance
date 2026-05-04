@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Mata Kuliah</h1>
        <a href="{{ route('admin.courses.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Kembali</a>
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

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="kode_mk" class="block text-sm font-medium text-gray-700 mb-1">Kode MK</label>
                <input type="text" name="kode_mk" id="kode_mk" value="{{ old('kode_mk', $course->kode_mk) }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('kode_mk') border-red-500 @enderror"
                    placeholder="e.g., MK101">
                @error('kode_mk')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="nama_mk" class="block text-sm font-medium text-gray-700 mb-1">Nama MK</label>
                <input type="text" name="nama_mk" id="nama_mk" value="{{ old('nama_mk', $course->nama_mk) }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_mk') border-red-500 @enderror"
                    placeholder="e.g., Pemrograman Web">
                @error('nama_mk')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="sks" class="block text-sm font-medium text-gray-700 mb-1">SKS</label>
                    <input type="number" name="sks" id="sks" value="{{ old('sks', $course->sks) }}" min="1" max="6"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('sks')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <input type="number" name="semester" id="semester" value="{{ old('semester', $course->semester) }}" min="1" max="8"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('semester')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="jurusan_id" class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                    <select name="jurusan_id" id="jurusan_id"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih --</option>
                        @foreach($jurusans as $jurusan)
                        <option value="{{ $jurusan->id }}" {{ old('jurusan_id', $course->jurusan_id) == $jurusan->id ? 'selected' : '' }}>{{ $jurusan->nama }}</option>
                        @endforeach
                    </select>
                    @error('jurusan_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Lecturers</label>
                @if($dosens->isNotEmpty())
                <div class="grid grid-cols-2 gap-2">
                    @foreach($dosens as $dosen)
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="lecturers[]" value="{{ $dosen->id }}"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            {{ in_array($dosen->id, old('lecturers', $assignedLecturerIds)) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">{{ $dosen->name }}</span>
                    </label>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400">Tidak ada dosen tersedia.</p>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Perbarui
                </button>
                <a href="{{ route('admin.courses.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
