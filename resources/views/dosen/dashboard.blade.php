@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Selamat Datang, {{ Auth::user()->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">Dashboard Dosen - Smart Attendance System</p>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
            <div class="bg-white overflow-hidden rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Mata Kuliah</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $courses->count() }}</dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Jadwal Hari Ini</dt>
                    <dd class="mt-1 text-3xl font-semibold text-indigo-600">{{ $todaySchedules->count() }}</dd>
                </div>
            </div>
            <div class="bg-white overflow-hidden rounded-lg border border-gray-200 shadow-sm">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Sesi Aktif</dt>
                    <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $activeSessions->count() }}</dd>
                </div>
            </div>
        </div>

        {{-- Today's Schedules --}}
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Jadwal Hari Ini</h2>

            @if($todaySchedules->isEmpty())
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 text-center">
                    <p class="text-gray-500">Tidak ada jadwal hari ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($todaySchedules as $schedule)
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $schedule->course->nama_mk ?? $schedule->course->kode_mk }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $schedule->hari }}, {{ $schedule->jam_mulai }} - {{ $schedule->jam_selesai }}</p>
                                <p class="text-sm text-gray-500 mt-1">Ruang: {{ $schedule->ruang }}</p>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('dosen.sessions.create', ['schedule' => $schedule->id]) }}"
                                   class="inline-flex items-center justify-center w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                    Buka Sesi Absen
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Active Sessions --}}
        @if($activeSessions->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Sesi Sedang Berlangsung</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach($activeSessions as $session)
                        <div class="bg-white rounded-lg border border-green-200 shadow-sm p-5 ring-1 ring-green-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                                    <h3 class="font-semibold text-gray-900 mt-2">{{ $session->course->nama_mk ?? '-' }}</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ $session->schedule->jam_mulai ?? '' }} - {{ $session->schedule->jam_selesai ?? '' }} | {{ $session->schedule->ruang ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">Berakhir: {{ $session->expires_at->format('H:i') }}</p>
                                </div>
                                <a href="{{ route('dosen.sessions.show', $session) }}"
                                   class="rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 transition-colors">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Recent Sessions --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Sesi Terbaru</h2>
                <a href="{{ route('dosen.sessions.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Lihat Semua</a>
            </div>

            @if($recentSessions->isEmpty())
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-8 text-center">
                    <p class="text-gray-500">Belum ada sesi absensi.</p>
                </div>
            @else
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Hadir</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentSessions as $session)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <a href="{{ route('dosen.sessions.show', $session) }}" class="text-indigo-600 hover:text-indigo-500">
                                            {{ $session->course->nama_mk ?? '-' }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $session->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $session->created_at->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($session->status === 'aktif')
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">Ditutup</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $session->attendances_count ?? $session->attendances->count() ?? 0 }} / {{ $session->total_mahasiswa }}
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
