@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Jurusan</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.jurusans.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="kode" class="block text-sm font-medium text-gray-700 mb-1">Kode Jurusan</label>
                    <input type="text" name="kode" id="kode" value="{{ old('kode') }}" class="rounded-lg border border-gray-300 px-3 py-2 w-full max-w-xs text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="contoh: AB" maxlength="10" required>
                    @error('kode')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Jurusan</label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama') }}" class="rounded-lg border border-gray-300 px-3 py-2 w-full max-w-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="contoh: Administrasi Bisnis" required>
                    @error('nama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mt-6 flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.jurusans.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
