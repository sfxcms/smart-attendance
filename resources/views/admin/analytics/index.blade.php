@extends('layouts.app')

@section('title', 'Analitik Kehadiran')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Analitik Kehadiran</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">&larr; Kembali ke Dashboard</a>
    </div>

    {{-- Overview Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-medium text-gray-500">Total Presensi</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $overview['total_attendance'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-medium text-gray-500">Hadir</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $overview['total_hadir'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-medium text-gray-500">Izin</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $overview['total_izin'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-medium text-gray-500">Sakit</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $overview['total_sakit'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-medium text-gray-500">Alpha</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $overview['total_alpha'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-medium text-gray-500">Tingkat Kehadiran</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $overview['attendance_rate'] }}%</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-medium text-gray-500">Mhs Aktif</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $overview['mhs_with_attendance'] }}/{{ $overview['total_mahasiswa'] }}</p>
        </div>
    </div>

    {{-- Full-width Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Kehadiran per Jurusan --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Kehadiran per Jurusan</h2>
            <canvas id="chartJurusan" height="250"></canvas>
        </div>

        {{-- Kehadiran per Semester --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Kehadiran per Semester</h2>
            <canvas id="chartSemester" height="250"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 mb-8">
        {{-- Tren 30 Hari (full width) --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tren Presensi 30 Hari Terakhir</h2>
            <canvas id="chartTrend" height="100"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6">
        {{-- Top/Bottom Courses (full width) --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tingkat Kehadiran per Mata Kuliah (Top 20)</h2>
            <canvas id="chartCourse" height="150"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
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
                scales: { y: { beginAtZero: true, max: 100, title: { display: true, text: '%' } } },
                plugins: { legend: { display: false } }
            }
        });
    }

    const semesterData = @json($bySemester);
    if (semesterData.labels.length > 0) {
        new Chart(document.getElementById('chartSemester'), {
            type: 'bar',
            data: {
                labels: semesterData.labels,
                datasets: [
                    { label: 'Hadir', data: semesterData.hadir, backgroundColor: 'rgba(59, 130, 246, 0.7)' },
                    { label: 'Izin', data: semesterData.izin, backgroundColor: 'rgba(245, 158, 11, 0.7)' },
                    { label: 'Sakit', data: semesterData.sakit, backgroundColor: 'rgba(239, 68, 68, 0.7)' },
                    { label: 'Alpha', data: semesterData.alpha, backgroundColor: 'rgba(107, 114, 128, 0.7)' }
                ]
            },
            options: {
                responsive: true,
                scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }
            }
        });
    }

    const trendData = @json($trend);
    if (trendData.labels.length > 0) {
        new Chart(document.getElementById('chartTrend'), {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [
                    { label: 'Hadir', data: trendData.hadir, borderColor: 'rgb(59, 130, 246)', backgroundColor: 'rgba(59, 130, 246, 0.1)', fill: true, tension: 0.3 },
                    { label: 'Izin', data: trendData.izin, borderColor: 'rgb(245, 158, 11)', backgroundColor: 'rgba(245, 158, 11, 0.1)', fill: true, tension: 0.3 },
                    { label: 'Sakit', data: trendData.sakit, borderColor: 'rgb(239, 68, 68)', backgroundColor: 'rgba(239, 68, 68, 0.1)', fill: true, tension: 0.3 }
                ]
            },
            options: {
                responsive: true,
                interaction: { intersect: false, mode: 'index' },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    const courseData = @json($byCourse);
    if (courseData.labels.length > 0) {
        const sorted = courseData.labels.map((l, i) => ({ label: l, rate: courseData.rates[i] }))
            .sort((a, b) => b.rate - a.rate);

        new Chart(document.getElementById('chartCourse'), {
            type: 'bar',
            data: {
                labels: sorted.map(s => s.label.length > 35 ? s.label.substring(0, 35) + '...' : s.label),
                datasets: [{
                    label: 'Kehadiran (%)',
                    data: sorted.map(s => s.rate),
                    backgroundColor: sorted.map(s => s.rate >= 80 ? 'rgba(16, 185, 129, 0.7)' :
                                                     s.rate >= 60 ? 'rgba(245, 158, 11, 0.7)' :
                                                     'rgba(239, 68, 68, 0.7)'),
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: { x: { beginAtZero: true, max: 100, title: { display: true, text: '%' } } },
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>
@endpush
