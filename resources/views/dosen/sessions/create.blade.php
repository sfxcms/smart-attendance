@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('dosen.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Dashboard
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Buka Sesi Absensi</h1>
            <p class="mt-1 text-sm text-gray-600">Pilih jadwal untuk memulai sesi absensi baru.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-200">
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('dosen.sessions.store') }}" id="sessionForm">
            @csrf

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="p-6">
                    <label for="schedule_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Jadwal</label>
                    <select name="schedule_id" id="schedule_id"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2"
                            required>
                        <option value="">-- Pilih Jadwal --</option>
                        @foreach($schedules as $s)
                            <option value="{{ $s->id }}" {{ isset($schedule) && $schedule->id === $s->id ? 'selected' : '' }}>
                                {{ $s->course->nama_mk ?? $s->course->kode_mk }} - {{ $s->hari }}, {{ $s->jam_mulai }}-{{ $s->jam_selesai }} ({{ $s->ruang }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipe Sesi --}}
                <div class="border-t border-gray-200 p-6">
                    <span class="block text-sm font-medium text-gray-700 mb-3">Tipe Sesi</span>
                    <div class="flex gap-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipe_sesi" value="offline" checked
                                   class="rounded-full border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700">Offline (QR di ruang kelas)</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tipe_sesi" value="online"
                                   class="rounded-full border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-700">Online (via meeting link)</span>
                        </label>
                    </div>
                </div>

                {{-- Link Meeting (online only) --}}
                <div id="linkMeetingSection" class="hidden border-t border-gray-200 p-6 bg-gray-50">
                    <label for="link_meeting" class="block text-sm font-medium text-gray-700 mb-2">
                        Link Meeting <span class="text-red-500">*</span>
                    </label>
                    <input type="url" name="link_meeting" id="link_meeting"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-2"
                           placeholder="https://zoom.us/j/... atau https://meet.google.com/...">
                    <p class="mt-1 text-xs text-gray-500">Masukkan link Zoom, Google Meet, atau platform meeting lainnya.</p>
                </div>

                {{-- Course detail preview --}}
                <div id="courseDetail" class="hidden border-t border-gray-200 p-6 bg-gray-50">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Detail Mata Kuliah</h3>
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                        <dt class="text-gray-500">Mata Kuliah</dt>
                        <dd id="detailCourse" class="text-gray-900 font-medium">-</dd>
                        <dt class="text-gray-500">Hari</dt>
                        <dd id="detailDay" class="text-gray-900">-</dd>
                        <dt class="text-gray-500">Jam</dt>
                        <dd id="detailTime" class="text-gray-900">-</dd>
                        <dt class="text-gray-500">Ruang</dt>
                        <dd id="detailRoom" class="text-gray-900">-</dd>
                    </dl>
                </div>

                <div class="flex justify-end border-t border-gray-200 p-6">
                    <button type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        Mulai Sesi Absen
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@php
    $scheduleData = $schedules->mapWithKeys(fn($s) => [
        $s->id => [
            'course' => $s->course->nama_mk ?? $s->course->kode_mk ?? '-',
            'day'    => $s->hari,
            'start'  => $s->jam_mulai,
            'end'    => $s->jam_selesai,
            'room'   => $s->ruang,
        ]
    ]);
@endphp
<script>
    const schedules = {!! json_encode($scheduleData) !!};

    // Toggle link meeting field based on tipe sesi
    document.querySelectorAll('input[name="tipe_sesi"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            const section = document.getElementById('linkMeetingSection');
            if (this.value === 'online') {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
                document.getElementById('link_meeting').value = '';
            }
        });
    });

    document.getElementById('schedule_id').addEventListener('change', function () {
        const detail = document.getElementById('courseDetail');
        const val = this.value;

        if (val && schedules[val]) {
            const s = schedules[val];
            document.getElementById('detailCourse').textContent = s.course;
            document.getElementById('detailDay').textContent = s.day;
            document.getElementById('detailTime').textContent = s.start + ' - ' + s.end;
            document.getElementById('detailRoom').textContent = s.room;
            detail.classList.remove('hidden');
        } else {
            detail.classList.add('hidden');
        }
    });
</script>
@endsection
