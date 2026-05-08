<?php

namespace Tests\Feature;

use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Jurusan;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_web_login_sets_http_only_and_same_site_session_cookie(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $sessionCookie = collect($response->headers->getCookies())
            ->first(fn ($cookie) => $cookie->getName() === config('session.cookie'));

        $this->assertNotNull($sessionCookie);
        $this->assertTrue($sessionCookie->isHttpOnly());
        $this->assertSame(config('session.same_site'), strtolower((string) $sessionCookie->getSameSite()));
    }

    public function test_api_login_issues_token_with_four_hour_expiration_window(): void
    {
        Carbon::setTestNow('2026-05-08 10:00:00');

        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()->assertJson([
            'success' => true,
            'message' => 'Login berhasil.',
        ]);

        $token = PersonalAccessToken::query()->sole();

        $this->assertNotNull($token->expires_at);
        $this->assertTrue($token->expires_at->equalTo(now()->addMinutes(config('sanctum.expiration'))));

        Carbon::setTestNow();
    }

    public function test_mahasiswa_cannot_access_dosen_web_session_page(): void
    {
        [$mahasiswa, $session] = $this->createEnrolledMahasiswaAndSession();

        $response = $this->actingAs($mahasiswa)->get(route('dosen.sessions.show', $session));

        $response->assertRedirect(route('mahasiswa.dashboard'));
    }

    public function test_dosen_cannot_access_admin_course_index(): void
    {
        $dosen = User::factory()->create([
            'role' => 'dosen',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($dosen)->get(route('admin.courses.index'));

        $response->assertRedirect(route('dosen.dashboard'));
    }

    public function test_dosen_cannot_view_other_dosen_session_attendance_in_api(): void
    {
        [$owner, $session] = $this->createOwnedSession();
        [$intruder] = $this->createOwnedSession();

        $response = $this->actingAs($intruder, 'sanctum')
            ->getJson('/api/dosen/sessions/'.$session->id.'/attendance');

        $response->assertForbidden()->assertJson([
            'success' => false,
            'message' => 'Anda tidak memiliki akses ke sesi ini.',
        ]);
    }

    public function test_dosen_cannot_close_other_dosen_session_in_api(): void
    {
        [$owner, $session] = $this->createOwnedSession();
        [$intruder] = $this->createOwnedSession();

        $response = $this->actingAs($intruder, 'sanctum')
            ->postJson('/api/dosen/sessions/'.$session->id.'/close');

        $response->assertForbidden()->assertJson([
            'success' => false,
            'message' => 'Anda tidak memiliki akses ke sesi ini.',
        ]);

        $this->assertSame('aktif', $session->fresh()->status);
    }

    public function test_mahasiswa_cannot_access_dosen_api_endpoints(): void
    {
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($mahasiswa, 'sanctum')->getJson('/api/dosen/sessions');

        $response->assertForbidden()->assertJson([
            'success' => false,
            'message' => 'Anda tidak memiliki akses.',
        ]);
    }

    public function test_dosen_cannot_use_mahasiswa_api_scan_endpoint(): void
    {
        $dosen = User::factory()->create([
            'role' => 'dosen',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($dosen, 'sanctum')->postJson('/api/attendance/scan', [
            'qr_data' => 'https://example.test/attendance/scan/1?token=fake',
        ]);

        $response->assertForbidden()->assertJson([
            'success' => false,
            'message' => 'Anda tidak memiliki akses.',
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
