@extends('layouts.app')

@section('title', 'Statistik Kehadiran - ' . $course->nama_mk)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('dosen.students.index') }}" class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Daftar Mata Kuliah
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $course->nama_mk }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $course->kode_mk }} &middot; {{ $course->jurusan->nama ?? '-' }} &middot; Semester {{ $course->semester }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-sm text-gray-500 mb-1">Total Mahasiswa</p>
                <p class="text-3xl font-bold text-gray-900">{{ count($studentData) }}</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-sm text-gray-500 mb-1">Total Sesi</p>
                <p class="text-3xl font-bold text-indigo-600">{{ $totalSessions }}</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-sm text-gray-500 mb-1">Kehadiran Tertinggi</p>
                <p class="text-3xl font-bold text-green-600">
                    {{ count($studentData) > 0 ? max(array_column($studentData, 'persentase')) : 0 }}%
                </p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-sm text-gray-500 mb-1">Kehadiran Terendah</p>
                <p class="text-3xl font-bold text-red-600">
                    {{ count($studentData) > 0 ? min(array_column($studentData, 'persentase')) : 0 }}%
                </p>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Statistik Kehadiran Mahasiswa</h2>
                <span class="text-sm text-gray-500">{{ $totalSessions }} sesi</span>
            </div>

            @if(empty($studentData))
                <div class="p-12 text-center">
                    <p class="text-gray-500">Belum ada data mahasiswa untuk mata kuliah ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hadir</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Izin</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Sakit</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Alpha</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Belum</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($studentData as $i => $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $student['nim'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">{{ $student['hadir'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">{{ $student['izin'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-800">{{ $student['sakit'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">{{ $student['alpha'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">{{ $student['sisa'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold
                                    {{ $student['persentase'] >= 80 ? 'text-green-700' : ($student['persentase'] >= 60 ? 'text-yellow-700' : 'text-red-700') }}">
                                    {{ $student['persentase'] }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
