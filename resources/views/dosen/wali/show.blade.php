@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $student->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">
                NIM: {{ $student->nim ?? '-' }} &middot;
                Jurusan: {{ $student->jurusan->nama ?? '-' }} &middot;
                Dosen Wali: {{ $student->dosenWali->name ?? '-' }}
            </p>
        </div>
        <a href="{{ route('dosen.wali.index') }}" class="text-sm text-gray-600 hover:text-gray-800">Kembali</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $attendance->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $attendance->attendanceSession->course->nama_mk ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            @if($attendance->attendanceSession->schedule)
                                {{ $attendance->attendanceSession->schedule->hari }} {{ $attendance->attendanceSession->schedule->jam_mulai }}-{{ $attendance->attendanceSession->schedule->jam_selesai }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $statusColors = [
                                    'hadir' => 'bg-green-100 text-green-800',
                                    'izin' => 'bg-yellow-100 text-yellow-800',
                                    'sakit' => 'bg-blue-100 text-blue-800',
                                    'alpha' => 'bg-red-100 text-red-800',
                                ];
                                $color = $statusColors[$attendance->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                            Belum ada data absensi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
