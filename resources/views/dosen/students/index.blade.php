@extends('layouts.app')

@section('title', 'Mahasiswa Saya')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Mahasiswa yang Diajar</h1>
            <p class="mt-1 text-sm text-gray-600">Daftar mata kuliah yang Anda ajar beserta jumlah mahasiswa dan statistik kehadiran.</p>
        </div>

        @if($courses->isEmpty())
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 text-center">
                <p class="text-gray-500">Anda belum memiliki mata kuliah yang diajar.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @foreach($courses as $course)
                    @php
                        $stats = $courseStats[$course->id] ?? [
                            'total_students' => 0,
                            'total_sessions' => 0,
                            'total_attendance' => 0,
                        ];
                    @endphp
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $course->nama_mk }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ $course->kode_mk }} &middot; {{ $course->jurusan->nama ?? '-' }}</p>
                                    <p class="text-sm text-gray-500">Semester {{ $course->semester }}</p>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-800">
                                    {{ $stats['total_students'] }} Mahasiswa
                                </span>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-6">
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-500 mb-1">Sesi</p>
                                    <p class="text-xl font-bold text-gray-900">{{ $stats['total_sessions'] }}</p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <p class="text-xs text-green-600 mb-1">Absensi</p>
                                    <p class="text-xl font-bold text-green-700">{{ $stats['total_attendance'] }}</p>
                                </div>
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <p class="text-xs text-blue-600 mb-1">Rata-rata</p>
                                    <p class="text-xl font-bold text-blue-700">
                                        {{ $stats['total_sessions'] > 0 ? round($stats['total_attendance'] / max(1, $stats['total_students'] * $stats['total_sessions']) * 100, 0) : 0 }}%
                                    </p>
                                </div>
                            </div>

                            <a href="{{ route('dosen.students.show', $course) }}"
                               class="block w-full text-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                Lihat Statistik Kehadiran
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
