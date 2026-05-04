@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Admin</h1>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm font-medium text-gray-500">Total Mata Kuliah</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalCourses }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm font-medium text-gray-500">Total Dosen</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalDosen }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm font-medium text-gray-500">Total Mahasiswa</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalMahasiswa }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm font-medium text-gray-500">Total Jadwal</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalSchedules }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm font-medium text-gray-500">Sesi Aktif</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $activeSessions }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm font-medium text-gray-500">Total Presensi</p>
            <p class="text-3xl font-bold text-gray-800 mt-1">{{ $totalAttendance }}</p>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Chart 1: Kehadiran per Jurusan --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Kehadiran per Jurusan</h2>
            <canvas id="chartJurusan" height="200"></canvas>
        </div>

        {{-- Chart 2: Kehadiran per Semester --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Kehadiran per Semester</h2>
            <canvas id="chartSemester" height="200"></canvas>
        </div>

        {{-- Chart 3: Tren 30 Hari --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tren Presensi 30 Hari</h2>
            <canvas id="chartTrend" height="200"></canvas>
        </div>

        {{-- Chart 4: Top/Bottom Courses --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tingkat Kehadiran per Course</h2>
            <canvas id="chartCourse" height="200"></canvas>
        </div>
    </div>

    {{-- Recent Sessions Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Sesi Presensi Terbaru</h2>
            <a href="{{ route('admin.analytics.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat Analitik Lengkap &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentSessions as $session)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $session->course->nama_mk ?? ($session->course->nama ?? '-') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $session->dosen->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $session->created_at ? $session->created_at->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($session->status === 'aktif')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                            @elseif($session->status === 'ditutup')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Ditutup</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ $session->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                            Belum ada sesi presensi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Chart 1: Kehadiran per Jurusan
    const jurusanData = @json($byJurusan);
    if (jurusanData.labels.length > 0) {
        new Chart(document.getElementById('chartJurusan'), {
            type: 'bar',
            data: {
                labels: jurusanData.labels,
                datasets: [{
                    label: 'Tingkat Kehadiran (%)',
                    data: jurusanData.rates,
                    backgroundColor: ['rgba(59, 130, 246, 0.7)', 'rgba(16, 185, 129, 0.7)'],
                    borderColor: ['rgb(59, 130, 246)', 'rgb(16, 185, 129)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, max: 100, title: { display: true, text: '%' } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // Chart 2: Kehadiran per Semester
    const semesterData = @json($bySemester);
    if (semesterData.labels.length > 0) {
        new Chart(document.getElementById('chartSemester'), {
            type: 'bar',
            data: {
                labels: semesterData.labels,
                datasets: [{
                    label: 'Hadir',
                    data: semesterData.hadir,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                }, {
                    label: 'Izin',
                    data: semesterData.izin,
                    backgroundColor: 'rgba(245, 158, 11, 0.7)',
                }, {
                    label: 'Sakit',
                    data: semesterData.sakit,
                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                }, {
                    label: 'Alpha',
                    data: semesterData.alpha,
                    backgroundColor: 'rgba(107, 114, 128, 0.7)',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                }
            }
        });
    }

    // Chart 3: Tren 30 Hari
    const trendData = @json($trend);
    if (trendData.labels.length > 0) {
        new Chart(document.getElementById('chartTrend'), {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [{
                    label: 'Hadir',
                    data: trendData.hadir,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.3
                }, {
                    label: 'Izin',
                    data: trendData.izin,
                    borderColor: 'rgb(245, 158, 11)',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    fill: true,
                    tension: 0.3
                }, {
                    label: 'Sakit',
                    data: trendData.sakit,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                interaction: { intersect: false, mode: 'index' },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Jumlah' } }
                }
            }
        });
    }

    // Chart 4: Top Courses by Attendance Rate
    const courseData = @json($byCourse);
    if (courseData.labels.length > 0) {
        const sorted = courseData.labels.map((l, i) => ({ label: l, rate: courseData.rates[i] }))
            .sort((a, b) => b.rate - a.rate)
            .slice(0, 10);

        new Chart(document.getElementById('chartCourse'), {
            type: 'bar',
            data: {
                labels: sorted.map(s => s.label.length > 20 ? s.label.substring(0, 20) + '...' : s.label),
                datasets: [{
                    label: 'Kehadiran (%)',
                    data: sorted.map(s => s.rate),
                    backgroundColor: sorted.map(s => s.rate >= 80 ? 'rgba(16, 185, 129, 0.7)' :
                                                     s.rate >= 60 ? 'rgba(245, 158, 11, 0.7)' :
                                                     'rgba(239, 68, 68, 0.7)'),
                    borderColor: sorted.map(s => s.rate >= 80 ? 'rgb(16, 185, 129)' :
                                                     s.rate >= 60 ? 'rgb(245, 158, 11)' :
                                                     'rgb(239, 68, 68)'),
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: { beginAtZero: true, max: 100, title: { display: true, text: '%' } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>
@endpush
