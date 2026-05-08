<?php

namespace Tests\Feature;

use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Jurusan;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_session_page_renders_qr_for_mahasiswa_scan_route(): void
    {
        [$dosen, $session] = $this->createOwnedSession();

        $response = $this->actingAs($dosen)->get(route('dosen.sessions.show', $session));

        $response->assertOk();
        $response->assertSee('value="'.config('app.url').'/attendance/scan/'.$session->id.'?token='.$session->qr_code.'"', false);
    }

    public function test_mahasiswa_web_scan_records_attendance_without_mutating_registered_total(): void
    {
        [$mahasiswa, $session] = $this->createEnrolledMahasiswaAndSession();
        $originalTotal = $session->total_mahasiswa;

        $response = $this->actingAs($mahasiswa)->post(route('mahasiswa.attendance.scan'), [
            'qr_data' => config('app.url').'/attendance/scan/'.$session->id.'?token='.$session->qr_code,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'attendance_session_id' => $session->id,
            'user_id' => $mahasiswa->id,
            'status' => 'hadir',
        ]);

        $this->assertSame($originalTotal, $session->fresh()->total_mahasiswa);
    }

    public function test_mahasiswa_web_scan_accepts_attendance_scan_route_payload(): void
    {
        [$mahasiswa, $session] = $this->createEnrolledMahasiswaAndSession();

        $response = $this->actingAs($mahasiswa)
            ->postJson(route('mahasiswa.attendance.scan'), [
                'qr_data' => config('app.url').'/attendance/scan/'.$session->id.'?token='.$session->qr_code,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Absensi berhasil!',
            ]);

        $this->assertDatabaseHas('attendances', [
            'attendance_session_id' => $session->id,
            'user_id' => $mahasiswa->id,
        ]);
    }

    public function test_mahasiswa_web_scan_rejects_missing_token(): void
    {
        [$mahasiswa, $session] = $this->createEnrolledMahasiswaAndSession();

        $response = $this->actingAs($mahasiswa)->postJson(route('mahasiswa.attendance.scan'), [
            'qr_data' => config('app.url').'/attendance/scan/'.$session->id,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Token QR tidak valid.',
            ]);

        $this->assertDatabaseMissing('attendances', [
            'attendance_session_id' => $session->id,
            'user_id' => $mahasiswa->id,
        ]);
    }

    public function test_mahasiswa_web_scan_rejects_forged_token(): void
    {
        [$mahasiswa, $session] = $this->createEnrolledMahasiswaAndSession();

        $response = $this->actingAs($mahasiswa)->postJson(route('mahasiswa.attendance.scan'), [
            'qr_data' => config('app.url').'/attendance/scan/'.$session->id.'?token=forged-token',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Token QR tidak valid.',
            ]);

        $this->assertDatabaseMissing('attendances', [
            'attendance_session_id' => $session->id,
            'user_id' => $mahasiswa->id,
        ]);
    }

    public function test_mahasiswa_web_scan_rejects_mismatched_session_and_token(): void
    {
        [$mahasiswa, $session] = $this->createEnrolledMahasiswaAndSession();
        [, $otherSession] = $this->createOwnedSession();

        $response = $this->actingAs($mahasiswa)->postJson(route('mahasiswa.attendance.scan'), [
            'qr_data' => config('app.url').'/attendance/scan/'.$session->id.'?token='.$otherSession->qr_code,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Token QR tidak valid.',
            ]);

        $this->assertDatabaseMissing('attendances', [
            'attendance_session_id' => $session->id,
            'user_id' => $mahasiswa->id,
        ]);
    }

    private function createOwnedSession(): array
    {
        $suffix = fake()->unique()->numerify('###');

        $jurusan = Jurusan::create([
            'nama' => 'Teknik Informatika '.$suffix,
            'kode' => 'TI'.$suffix,
        ]);

        $dosen = User::factory()->create([
            'role' => 'dosen',
            'nip' => 'D-'.fake()->unique()->numerify('#####'),
            'jurusan_id' => $jurusan->id,
        ]);

        $course = new Course([
            'kode_mk' => 'IF'.fake()->unique()->numerify('###'),
            'nama_mk' => 'Algoritma '.$suffix,
            'sks' => 3,
            'semester' => 1,
        ]);
        $course->jurusan()->associate($jurusan);
        $course->save();

        $course->lecturers()->attach($dosen->id);

        $schedule = Schedule::create([
            'course_id' => $course->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '09:40:00',
            'ruang' => 'Lab 1',
            'kelompok' => 'A',
        ]);

        $session = AttendanceSession::create([
            'schedule_id' => $schedule->id,
            'course_id' => $course->id,
            'dosen_id' => $dosen->id,
            'status' => 'aktif',
            'tipe_sesi' => 'offline',
            'link_meeting' => null,
            'expires_at' => now()->addMinutes(15),
            'qr_code' => 'qr_test_token_'.fake()->unique()->numerify('#####'),
            'total_mahasiswa' => 30,
        ]);

        return [$dosen, $session];
    }

    private function createEnrolledMahasiswaAndSession(): array
    {
        [$dosen, $session] = $this->createOwnedSession();

        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'nim' => 'M-'.fake()->unique()->numerify('#####'),
            'jurusan_id' => $session->course->jurusan_id,
        ]);

        Enrollment::create([
            'user_id' => $mahasiswa->id,
            'jurusan_id' => $session->course->jurusan_id,
            'semester' => $session->course->semester,
        ]);

        return [$mahasiswa, $session->fresh()];
    }
}
