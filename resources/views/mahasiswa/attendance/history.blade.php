@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Absensi</h1>
            <p class="mt-1 text-sm text-gray-600">Riwayat absensi perkuliahan Anda.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-5">
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-4 text-center">
                    <div class="text-sm font-medium text-gray-500">Total</div>
                    <div class="text-xl font-semibold text-gray-900">{{ $totalAttendance }}</div>
                </div>
            </div>
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-4 text-center">
                    <div class="text-sm font-medium text-gray-500">Hadir</div>
                    <div class="text-xl font-semibold text-green-600">{{ $totalHadir }}</div>
                </div>
            </div>
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-4 text-center">
                    <div class="text-sm font-medium text-gray-500">Izin</div>
                    <div class="text-xl font-semibold text-yellow-600">{{ $totalIzin }}</div>
                </div>
            </div>
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-4 text-center">
                    <div class="text-sm font-medium text-gray-500">Sakit</div>
                    <div class="text-xl font-semibold text-orange-600">{{ $totalSakit }}</div>
                </div>
            </div>
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-4 text-center">
                    <div class="text-sm font-medium text-gray-500">Alpha</div>
                    <div class="text-xl font-semibold text-red-600">{{ $totalAlpha }}</div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200">
                <form method="GET" action="{{ route('mahasiswa.attendance.history') }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700">Mata Kuliah</label>
                        <select name="course_id" id="course_id" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">Semua Mata Kuliah</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->nama_mk }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700">Bulan</label>
                        <select name="month" id="month" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">Semua Bulan</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alpha" {{ request('status') == 'alpha' ? 'selected' : '' }}>Alpha</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                        <a href="{{ route('mahasiswa.attendance.history') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            @if($attendances->isEmpty())
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data absensi</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada catatan absensi yang sesuai dengan filter Anda.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">No</th>
                                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tanggal</th>
                                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Mata Kuliah</th>
                                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Jadwal</th>
                                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Status</th>
                                <th scope="col" class="px-4 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Waktu Scan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($attendances as $index => $attendance)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $attendances->firstItem() + $index }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                        {{ $attendance->scanned_at ? \Carbon\Carbon::parse($attendance->scanned_at)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                        {{ $attendance->attendanceSession->course->nama_mk ?? 'Mata Kuliah' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                        @if($attendance->attendanceSession->schedule)
                                            {{ $attendance->attendanceSession->schedule->hari ?? '-' }}
                                            <br>
                                            {{ $attendance->attendanceSession->schedule->jam_mulai ? \Carbon\Carbon::parse($attendance->attendanceSession->schedule->jam_mulai)->format('H:i') : '-' }}
                                            -
                                            {{ $attendance->attendanceSession->schedule->jam_selesai ? \Carbon\Carbon::parse($attendance->attendanceSession->schedule->jam_selesai)->format('H:i') : '-' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @php
                                            $statusBadge = match($attendance->status) {
                                                'hadir' => 'bg-green-100 text-green-800',
                                                'izin' => 'bg-yellow-100 text-yellow-800',
                                                'sakit' => 'bg-orange-100 text-orange-800',
                                                'alpha' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                            $statusLabel = match($attendance->status) {
                                                'hadir' => 'Hadir',
                                                'izin' => 'Izin',
                                                'sakit' => 'Sakit',
                                                'alpha' => 'Alpha',
                                                default => $attendance->status,
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $attendance->scanned_at ? \Carbon\Carbon::parse($attendance->scanned_at)->format('H:i:s') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
