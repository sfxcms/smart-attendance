<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Smart Attendance') - Smart Attendance</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <nav class="bg-white border-b border-gray-200 shadow-sm relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Brand --}}
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-lg font-bold text-indigo-600 tracking-tight">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Smart Attendance
                    </a>
                </div>

                {{-- Desktop nav --}}
                @auth
                @php
                    $role = auth()->user()->role;
                    $r = request()->route()->getName();
                @endphp
                <div class="hidden md:flex items-center gap-1">
                    @if ($role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ $r === 'admin.dashboard' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.courses.index') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.courses') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Mata Kuliah
                        </a>
                        <a href="{{ route('admin.jurusans.index') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.jurusans') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Jurusan
                        </a>
                        <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.analytics') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Analitik
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.users') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            Pengguna
                        </a>
                        <a href="{{ route('admin.schedules.index') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.schedules') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Jadwal
                        </a>
                        <a href="{{ route('admin.enrollments.index') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.enrollments') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Pendaftaran
                        </a>
                        <a href="{{ route('admin.mahasiswa.index') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.mahasiswa') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            Mahasiswa
                        </a>
                    @elseif ($role === 'dosen')
                        <a href="{{ route('dosen.dashboard') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ $r === 'dosen.dashboard' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('dosen.sessions.index') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'dosen.sessions.index') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Sesi Saya
                        </a>
                        <a href="{{ route('dosen.sessions.create') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'dosen.sessions.create') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Buat Sesi
                        </a>
                            <div class="pt-3 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Mahasiswa</div>
                            <a href="{{ route('dosen.students.index') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'dosen.students') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Statistik Kehadiran
                            </a>
                            <a href="{{ route('dosen.wali.index') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'dosen.wali') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Bimbingan
                            </a>
                        @elseif ($role === 'mahasiswa')
                        <a href="{{ route('mahasiswa.dashboard') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ $r === 'mahasiswa.dashboard' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('mahasiswa.attendance.scan') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'mahasiswa.attendance.scan') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            Absen QR
                        </a>
                        <a href="{{ route('mahasiswa.attendance.history') }}" class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'mahasiswa.attendance.history') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Riwayat
                        </a>
                    @endif
                </div>
                @endauth

                {{-- User info & logout / Guest links --}}
                <div class="flex items-center gap-3">
                    @auth
                    <div class="hidden md:flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize
                            @if($role === 'admin') bg-indigo-100 text-indigo-800
                            @elseif($role === 'dosen') bg-emerald-100 text-emerald-800
                            @else bg-blue-100 text-blue-800
                            @endif
                        ">
                            {{ $role }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar
                        </button>
                    </form>

                    {{-- Mobile hamburger --}}
                    <button id="nav-toggle" class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    @endauth

                    @guest
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-indigo-600 bg-white border border-indigo-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-300 transition-colors shadow-sm">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                        Daftar
                    </a>
                    @endguest
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        @auth
        <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white">
            <div class="px-4 py-3 space-y-1">
                @if ($role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $r === 'admin.dashboard' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.courses.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.courses') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Mata Kuliah
                    </a>
                    <a href="{{ route('admin.jurusans.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.jurusans') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Jurusan
                    </a>
                    <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.analytics') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Analitik
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.users') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        Pengguna
                    </a>
                    <a href="{{ route('admin.schedules.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.schedules') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Jadwal
                    </a>
                    <a href="{{ route('admin.enrollments.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.enrollments') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Pendaftaran
                    </a>
                    <a href="{{ route('admin.mahasiswa.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'admin.mahasiswa') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        Mahasiswa
                    </a>
                @elseif ($role === 'dosen')
                    <a href="{{ route('dosen.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $r === 'dosen.dashboard' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('dosen.sessions.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'dosen.sessions.index') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Sesi Saya
                    </a>
                    <a href="{{ route('dosen.sessions.create') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'dosen.sessions.create') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Buat Sesi
                    </a>
                    <div class="pt-3 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Mahasiswa</div>
                    <a href="{{ route('dosen.students.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'dosen.students') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Statistik Kehadiran
                    </a>
                    <a href="{{ route('dosen.wali.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'dosen.wali') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Bimbingan
                    </a>
                @elseif ($role === 'mahasiswa')
                    <a href="{{ route('mahasiswa.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ $r === 'mahasiswa.dashboard' ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('mahasiswa.attendance.scan') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'mahasiswa.attendance.scan') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        Absen QR
                    </a>
                    <a href="{{ route('mahasiswa.attendance.history') }}" class="flex items-center gap-2.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ str_starts_with($r, 'mahasiswa.attendance.history') ? 'text-indigo-700 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Riwayat
                    </a>
                @endif

                <div class="pt-3 mt-2 border-t border-gray-100">
                    <div class="flex items-center gap-2 px-3 mb-3">
                        <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize
                            @if($role === 'admin') bg-indigo-100 text-indigo-800
                            @elseif($role === 'dosen') bg-emerald-100 text-emerald-800
                            @else bg-blue-100 text-blue-800
                            @endif
                        ">
                            {{ $role }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 w-full text-left px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endauth
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <script>
        document.getElementById('nav-toggle')?.addEventListener('click', function () {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
