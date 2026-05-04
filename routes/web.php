<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\MahasiswaController as AdminMahasiswaController;
use App\Http\Controllers\Dosen\AttendanceController as DosenAttendanceController;
use App\Http\Controllers\Dosen\DosenController;
use App\Http\Controllers\Dosen\SessionController;
use App\Http\Controllers\Dosen\StudentController;
use App\Http\Controllers\Dosen\WaliController;
use App\Http\Controllers\Mahasiswa\MahasiswaController;
use App\Http\Controllers\Mahasiswa\AttendanceController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Guest routes (login, register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return redirect()->route(auth()->user()->role . '.dashboard');
    })->name('dashboard');

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
        Route::resource('courses', CourseController::class);
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
        Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
        Route::get('/enrollments/create', [EnrollmentController::class, 'create'])->name('enrollments.create');
        Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
        Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
        Route::resource('jurusans', JurusanController::class)->except(['show']);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/mahasiswa', [AdminMahasiswaController::class, 'index'])->name('mahasiswa.index');
    });

    // Dosen routes
    Route::prefix('dosen')->middleware('role:dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');
        Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');
        Route::get('/sessions/create/{schedule?}', [SessionController::class, 'create'])->name('sessions.create');
        Route::post('/sessions', [SessionController::class, 'store'])->name('sessions.store');
        Route::get('/sessions/{session}', [SessionController::class, 'show'])->name('sessions.show');
        Route::get('/sessions/{session}/attendance', [DosenAttendanceController::class, 'bySession'])->name('sessions.attendance');
        Route::put('/attendance/{attendance}', [DosenAttendanceController::class, 'update'])->name('attendance.update');
        Route::get('/sessions/{session}/export', [DosenAttendanceController::class, 'export'])->name('sessions.export');
        Route::post('/sessions/{session}/close', [SessionController::class, 'close'])->name('sessions.close');
        Route::get('/wali', [WaliController::class, 'index'])->name('wali.index');
        Route::get('/wali/{id}', [WaliController::class, 'show'])->name('wali.show');
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{course}', [StudentController::class, 'show'])->name('students.show');
    });

    // Mahasiswa routes
    Route::prefix('mahasiswa')->middleware('role:mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', [MahasiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/attendance/scan', [AttendanceController::class, 'showScan'])->name('attendance.scan');
        Route::post('/attendance/scan', [AttendanceController::class, 'scan']);
        Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    });
});
