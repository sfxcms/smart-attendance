<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    public function bySession(AttendanceSession $session)
    {
        if ($session->dosen_id !== Auth::id()) {
            abort(403);
        }

        $session->load([
            'course',
            'schedule',
            'attendances.user' => function ($q) {
                $q->select('id', 'name', 'nim');
            },
        ]);

        return view('dosen.sessions.show', [
            'session'   => $session,
            'qrDataUri' => null,
            'stats'     => [
                'hadir'  => $session->attendances->where('status', 'hadir')->count(),
                'izin'   => $session->attendances->where('status', 'izin')->count(),
                'sakit'  => $session->attendances->where('status', 'sakit')->count(),
                'alpha'  => $session->attendances->where('status', 'alpha')->count(),
                'belum'  => $session->total_mahasiswa - $session->attendances->count(),
            ],
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $session = $attendance->attendanceSession;

        if ($session->dosen_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alpha',
        ]);

        $attendance->update(['status' => $validated['status']]);

        return back()->with('success', 'Status kehadiran berhasil diperbarui.');
    }

    public function export(AttendanceSession $session): StreamedResponse
    {
        if ($session->dosen_id !== Auth::id()) {
            abort(403);
        }

        $session->load([
            'course',
            'schedule',
            'attendances.user' => function ($q) {
                $q->select('id', 'name', 'nim');
            },
        ]);

        $filename = 'absensi_' . str_replace(' ', '_', $session->course->nama_mk) . '_' . $session->created_at->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return new StreamedResponse(function () use ($session) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['No', 'NIM', 'Nama', 'Status', 'Waktu Scan']);

            foreach ($session->attendances as $i => $attendance) {
                fputcsv($handle, [
                    $i + 1,
                    $attendance->user->nim ?? '-',
                    $attendance->user->name ?? '-',
                    $attendance->status,
                    $attendance->scanned_at ? $attendance->scanned_at->format('Y-m-d H:i:s') : '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
