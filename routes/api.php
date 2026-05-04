<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DosenController;
use App\Http\Controllers\Api\MahasiswaController;
use Illuminate\Support\Facades\Route;

// Guest routes (no auth required)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Authenticated routes (shared)
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Mahasiswa API — only mahasiswa role
Route::middleware(['auth:sanctum', 'role:mahasiswa', 'throttle:60,1'])->group(function () {
    Route::get('/schedules', [MahasiswaController::class, 'schedules']);
    Route::post('/attendance/scan', [MahasiswaController::class, 'scan']);
    Route::get('/attendance/history', [MahasiswaController::class, 'history']);
});

// Dosen API — only dosen role
Route::middleware(['auth:sanctum', 'role:dosen', 'throttle:60,1'])->group(function () {
    Route::get('/dosen/sessions', [DosenController::class, 'sessions']);
    Route::post('/dosen/sessions', [DosenController::class, 'createSession']);
    Route::get('/dosen/sessions/{id}/attendance', [DosenController::class, 'sessionAttendance']);
    Route::post('/dosen/sessions/{id}/close', [DosenController::class, 'closeSession']);
    Route::get('/dosen/courses', [DosenController::class, 'myCourses']);
    Route::get('/dosen/courses/{courseId}/schedules', [DosenController::class, 'courseSchedules']);
});
