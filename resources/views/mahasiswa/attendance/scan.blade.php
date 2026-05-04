@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Scan QR Absensi</h1>
            <p class="mt-1 text-sm text-gray-600">Arahkan kamera ke kode QR yang ditampilkan oleh dosen.</p>
        </div>

        @if(session('success'))
            <div class="relative px-4 py-3 mb-4 text-sm text-green-700 bg-green-100 border border-green-200 rounded-lg" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="relative px-4 py-3 mb-4 text-sm text-red-700 bg-red-100 border border-red-200 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div>
                <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">Scan dengan Kamera</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Izinkan akses kamera untuk memindai QR code.</p>
                    </div>
                    <div class="p-5">
                        <div id="qr-scanner" class="mx-auto overflow-hidden bg-gray-100 rounded-lg" style="max-width: 300px; height: 300px;"></div>
                        <div id="scan-status" class="mt-3 text-sm text-center text-gray-500">Menunggu scan...</div>
                    </div>
                </div>

                <div class="mt-4 overflow-hidden bg-white rounded-lg shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">Input Manual</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Masukkan kode sesi secara manual jika kamera tidak berfungsi.</p>
                    </div>
                    <div class="p-5">
                        <form id="manual-scan-form" action="{{ route('mahasiswa.attendance.scan') }}" method="POST">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="qr_data" id="manual-input" class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan kode sesi atau URL QR" required>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                                Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div>
                <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">Sesi Aktif Hari Ini</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Kelas yang sedang membuka sesi absensi.</p>
                    </div>
                    <div class="p-5">
                        @php
                            $hasActiveSession = $todaySchedules->contains(function($schedule) {
                                return $schedule->attendanceSessions->isNotEmpty();
                            });
                        @endphp

                        @if(!$hasActiveSession)
                            <div class="py-8 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Tidak ada sesi absensi aktif saat ini.</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($todaySchedules as $schedule)
                                    @php
                                        $activeSession = $schedule->attendanceSessions->first();
                                        $alreadyAttended = $activeSession && $todayAttendances->first(function($att) use ($activeSession) {
                                            return $att->attendance_session_id === $activeSession->id;
                                        });
                                    @endphp
                                    @if($activeSession)
                                        <div class="p-3 border border-gray-200 rounded-lg {{ $alreadyAttended ? 'bg-green-50 border-green-200' : 'bg-blue-50 border-blue-200' }}">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full {{ $alreadyAttended ? 'bg-green-500' : 'bg-blue-500' }}"></span>
                                                        <span class="text-sm font-medium text-gray-900">{{ $schedule->course->nama_mk ?? 'Mata Kuliah' }}</span>
                                                        @if($activeSession->tipe_sesi === 'online' || $activeSession->tipe_sesi?->value === 'online')
                                                            <span class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium text-blue-700 bg-blue-100 rounded">Online</span>
                                                        @endif
                                                    </div>
                                                    <div class="mt-1 text-xs text-gray-500">
                                                        {{ $schedule->jam_mulai ? \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i') : '-' }} - {{ $schedule->jam_selesai ? \Carbon\Carbon::parse($schedule->jam_selesai)->format('H:i') : '-' }}
                                                        @if(!$alreadyAttended && ($activeSession->tipe_sesi === 'online' || $activeSession->tipe_sesi?->value === 'online') && $activeSession->link_meeting)
                                                            &middot; <a href="{{ $activeSession->link_meeting }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-800 underline">Buka Meeting</a>
                                                        @endif
                                                        @if($schedule->ruang)
                                                            &middot; {{ $schedule->ruang }}
                                                        @endif
                                                    </div>
                                                </div>
                                                @if($alreadyAttended)
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-green-700 bg-green-100 rounded-full">Terverifikasi</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">Aktif</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 overflow-hidden bg-white rounded-lg shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900">Absensi Hari Ini</h2>
                        <p class="mt-0.5 text-xs text-gray-500">Riwayat absensi Anda hari ini.</p>
                    </div>
                    <div class="p-5">
                        @if($todayAttendances->isEmpty())
                            <div class="py-8 text-center">
                                <p class="text-sm text-gray-500">Belum ada absensi tercatat hari ini.</p>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach($todayAttendances as $attendance)
                                    <div class="flex items-center justify-between p-2 text-sm bg-green-50 rounded-lg">
                                        <span class="font-medium text-green-800">{{ $attendance->attendanceSession->course->nama_mk ?? 'Mata Kuliah' }}</span>
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-green-700 bg-green-100 rounded-full">Hadir</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scannerElement = document.getElementById('qr-scanner');
        const scanStatus = document.getElementById('scan-status');
        let html5QrCode = null;

        function startScanner() {
            if (typeof Html5Qrcode === 'undefined') {
                scanStatus.textContent = 'Memuat library scanner...';
                setTimeout(startScanner, 500);
                return;
            }

            html5QrCode = new Html5Qrcode('qr-scanner');

            html5QrCode.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                },
                function(decodedText) {
                    html5QrCode.stop().catch(function() {});
                    scanStatus.textContent = 'Memproses...';

                    fetch('{{ route('mahasiswa.attendance.scan') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ qr_data: decodedText })
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                            scanStatus.textContent = data.message || 'Absensi berhasil!';
                            scanStatus.className = 'mt-3 text-sm text-center font-medium text-green-600';

                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            scanStatus.textContent = data.message || 'Gagal melakukan absensi.';
                            scanStatus.className = 'mt-3 text-sm text-center font-medium text-red-600';

                            setTimeout(function() {
                                startScanner();
                                scanStatus.className = 'mt-3 text-sm text-center text-gray-500';
                                scanStatus.textContent = 'Menunggu scan...';
                            }, 2000);
                        }
                    })
                    .catch(function(error) {
                        scanStatus.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                        scanStatus.className = 'mt-3 text-sm text-center font-medium text-red-600';

                        setTimeout(function() {
                            startScanner();
                            scanStatus.className = 'mt-3 text-sm text-center text-gray-500';
                            scanStatus.textContent = 'Menunggu scan...';
                        }, 2000);
                    });
                },
                function(errorMessage) {
                    // Silently handle continuous scanning feedback
                }
            ).catch(function(err) {
                scanStatus.textContent = 'Gagal mengakses kamera. Gunakan input manual.';
                scanStatus.className = 'mt-3 text-sm text-center font-medium text-red-600';
            });
        }

        startScanner();
    });
</script>
@endpush
