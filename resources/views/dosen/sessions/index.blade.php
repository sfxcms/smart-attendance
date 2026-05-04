@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('dosen.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Dashboard
                </a>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">Riwayat Sesi Absensi</h1>
            </div>
            <a href="{{ route('dosen.sessions.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition-colors">
                Buka Sesi Baru
            </a>
        </div>

        {{-- Filter --}}
        <div class="mb-6">
            <form method="GET" action="{{ route('dosen.sessions.index') }}" class="flex items-center gap-3">
                <label for="status" class="text-sm font-medium text-gray-700">Filter Status:</label>
                <select name="status" id="status"
                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm border px-3 py-1.5"
                        onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="ditutup" {{ request('status') === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                </select>
            </form>
        </div>

        @if($sessions->isEmpty())
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-12 text-center">
                <p class="text-gray-500">Belum ada sesi absensi.</p>
            </div>
        @else
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta Hadir</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sessions as $session)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $session->course->nama_mk ?? '-' }}
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
                                    {{ $session->attendances_count ?? 0 }} / {{ $session->total_mahasiswa }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                    <a href="{{ route('dosen.sessions.show', $session) }}"
                                       class="text-indigo-600 hover:text-indigo-500 font-medium">Lihat</a>
                                    @if($session->status === 'aktif')
                                        <form method="POST" action="{{ route('dosen.sessions.close', $session) }}" class="inline"
                                              onsubmit="return confirm('Tutup sesi ini?')">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-500 font-medium">Tutup</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $sessions->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
