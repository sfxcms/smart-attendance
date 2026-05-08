@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('dosen.sessions.index') }}" class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Daftar Sesi
            </a>
        </div>

        {{-- Flash message --}}
        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 border border-green-200 p-4">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Session Info --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Session Details Card --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Sesi</h2>
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-gray-500">Mata Kuliah</dt>
                                <dd class="font-medium text-gray-900">{{ $session->course->nama_mk ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Kode MK</dt>
                                <dd class="font-medium text-gray-900">{{ $session->course->kode_mk ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Jadwal</dt>
                                <dd class="font-medium text-gray-900">
                                    {{ $session->schedule->hari ?? '-' }}, {{ $session->schedule->jam_mulai ?? '' }} - {{ $session->schedule->jam_selesai ?? '' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Ruang</dt>
                                <dd class="font-medium text-gray-900">{{ $session->schedule->ruang ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Dosen</dt>
                                <dd class="font-medium text-gray-900">{{ $session->dosen->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Tipe Sesi</dt>
                                <dd>
                                    @if($session->tipe_sesi === 'online' || $session->tipe_sesi?->value === 'online')
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">Online</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">Offline</span>
                                    @endif
                                </dd>
                            </div>
                            @if($session->tipe_sesi === 'online' || $session->tipe_sesi?->value === 'online')
                            <div>
                                <dt class="text-gray-500">Link Meeting</dt>
                                <dd class="font-medium text-indigo-600">
                                    <a href="{{ $session->link_meeting }}" target="_blank" rel="noopener noreferrer"
                                       class="hover:text-indigo-800 underline break-all">
                                        {{ $session->link_meeting }}
                                    </a>
                                </dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-gray-500">Status</dt>
                                <dd>
                                    @if($session->status === 'aktif')
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                                    @elseif($session->status === 'kedaluwarsa')
                                        <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">Kedaluwarsa</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">Ditutup</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Dibuka</dt>
                                <dd class="text-gray-900">{{ $session->created_at->format('d M Y, H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Berakhir</dt>
                                <dd class="text-gray-900">{{ $session->expires_at->format('d M Y, H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Actions --}}
                    <div class="border-t border-gray-200 p-4 space-y-2">
                        @if($session->status === 'aktif')
                            <form method="POST" action="{{ route('dosen.sessions.close', $session) }}"
                                  onsubmit="return confirm('Tutup sesi absensi ini?')">
                                @csrf
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 transition-colors">
                                    Tutup Sesi
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('dosen.sessions.export', $session) }}"
                           class="w-full inline-flex items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 shadow-sm hover:bg-gray-50 transition-colors">
                            Ekspor CSV
                        </a>
                    </div>
                </div>

                {{-- QR Code Card --}}
                @if($qrDataUri && $session->status === 'aktif')
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 text-center">
                        <h3 class="text-sm font-medium text-gray-700 mb-4">QR Code Absensi</h3>
                        <div class="flex justify-center">
                            <img src="{{ $qrDataUri }}" alt="QR Code" class="w-64 h-64">
                        </div>
                        <div class="mt-4 text-left">
                            <label for="scan-url" class="block text-xs font-medium text-gray-500 mb-1">Link scan cadangan</label>
                            <input
                                id="scan-url"
                                type="text"
                                readonly
                                value="{{ config('app.url') . '/attendance/scan/' . $session->id . '?token=' . $session->qr_code }}"
                                class="w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-xs text-gray-700"
                            >
                        </div>
                        <p class="text-xs text-gray-400 mt-3">Berakhir pada {{ $session->expires_at->format('H:i') }}</p>
                    </div>
                @endif

                {{-- Summary Card --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">Ringkasan Kehadiran</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-500">Total Terdaftar</dt>
                            <dd class="font-semibold text-gray-900">{{ $session->total_mahasiswa }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-green-700">Hadir</dt>
                            <dd class="font-semibold text-green-700">{{ $stats['hadir'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-yellow-700">Izin</dt>
                            <dd class="font-semibold text-yellow-700">{{ $stats['izin'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-orange-700">Sakit</dt>
                            <dd class="font-semibold text-orange-700">{{ $stats['sakit'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-red-700">Alpha</dt>
                            <dd class="font-semibold text-red-700">{{ $stats['alpha'] }}</dd>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <dt class="text-gray-400">Belum Scan</dt>
                            <dd class="font-semibold text-gray-400">{{ $stats['belum'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Right: Attendance Table --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Daftar Kehadiran</h2>
                    </div>

                    @if($session->attendances->isEmpty())
                        <div class="p-12 text-center">
                            <p class="text-gray-500">Belum ada mahasiswa yang melakukan absensi.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Scan</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($session->attendances as $i => $attendance)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $i + 1 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">
                                                {{ $attendance->user->nim ?? '-' }}
                                             </td>
                                             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                 {{ $attendance->user->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'hadir' => 'bg-green-100 text-green-800',
                                                        'izin'  => 'bg-yellow-100 text-yellow-800',
                                                        'sakit' => 'bg-orange-100 text-orange-800',
                                                        'alpha' => 'bg-red-100 text-red-800',
                                                    ];
                                                    $colorClass = $statusColors[$attendance->status] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="inline-flex items-center rounded-full {{ $colorClass }} px-2.5 py-0.5 text-xs font-medium">
                                                    {{ ucfirst($attendance->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $attendance->scanned_at ? $attendance->scanned_at->format('H:i:s') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <form method="POST" action="{{ route('dosen.attendance.update', $attendance) }}" class="inline-flex items-center gap-2">
                                                    @csrf @method('PATCH')
                                                    <select name="status"
                                                            class="rounded-md border-gray-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 border px-2 py-1">
                                                        @foreach(['hadir', 'izin', 'sakit', 'alpha'] as $s)
                                                            <option value="{{ $s }}" {{ $attendance->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit"
                                                            class="inline-flex items-center rounded-md bg-white px-2 py-1 text-xs font-medium text-gray-700 border border-gray-300 shadow-sm hover:bg-gray-50 transition-colors">
                                                        Simpan
                                                    </button>
                                                </form>
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
    </div>
</div>
@endsection
