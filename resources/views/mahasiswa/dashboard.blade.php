@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Mahasiswa</h1>
            <p class="mt-1 text-sm text-gray-600">Selamat datang, {{ Auth::user()->name }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Total Mata Kuliah</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ $courses->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Kelas Hari Ini</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ $todaySchedules->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-indigo-100 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Total Absensi</div>
                            <div class="text-2xl font-semibold text-gray-900">{{ $totalAttendance }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-500">Kehadiran</div>
                            <div class="text-2xl font-semibold text-green-600">{{ $totalHadir }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-3">
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Hadir</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ $totalHadir }}</span>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Izin</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ $totalIzin }}</span>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Sakit</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">{{ $totalSakit }}</span>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">Alpha</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $totalAlpha }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Jadwal Hari Ini</h2>
                <div class="flex gap-2">
                    <a href="{{ route('mahasiswa.attendance.scan') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        Scan QR
                    </a>
                    <a href="{{ route('mahasiswa.attendance.history') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Lihat Riwayat
                    </a>
                </div>
            </div>

            @if($todaySchedules->isEmpty())
                <div class="p-8 text-center bg-white rounded-lg shadow-sm">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada jadwal hari ini</h3>
                    <p class="mt-1 text-sm text-gray-500">Anda tidak memiliki jadwal perkuliahan untuk hari ini.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($todaySchedules as $schedule)
                        @php
                            $activeSession = $schedule->attendanceSessions->first();
                            $alreadyAttended = $todayAttendances->first(function($att) use ($activeSession) {
                                return $activeSession && $att->attendance_session_id === $activeSession->id;
                            });
                        @endphp
                        <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                            <div class="p-5">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-base font-semibold text-gray-900">{{ $schedule->course->nama_mk ?? 'Mata Kuliah' }}</h3>
                                            @if($schedule->course->kode_mk)
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-full">{{ $schedule->course->kode_mk }}</span>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap items-center gap-4 mt-2 text-sm text-gray-600">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $schedule->jam_mulai ? \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i') : '-' }} - {{ $schedule->jam_selesai ? \Carbon\Carbon::parse($schedule->jam_selesai)->format('H:i') : '-' }}
                                            </span>
                                            @if($schedule->ruang)
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                    {{ $schedule->ruang }}
                                                </span>
                                            @endif
                                            @if($schedule->kelompok)
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    Semester {{ $schedule->kelompok }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($schedule->course->lecturers->isNotEmpty())
                                            <div class="mt-2 text-sm text-gray-500">
                                                Dosen: {{ $schedule->course->lecturers->pluck('name')->implode(', ') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-shrink-0 ml-4">
                                        @if($alreadyAttended)
                                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-700 bg-green-100 rounded-full">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Sudah Absen
                                            </span>
                                        @elseif($activeSession)
                                            <a href="{{ route('mahasiswa.attendance.scan') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                                                Absen Sekarang
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-500 bg-gray-100 rounded-full">
                                                Belum Ada Sesi
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
