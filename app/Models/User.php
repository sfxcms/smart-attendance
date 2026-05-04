<?php

namespace App\Models;

use App\Enums\RoleEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'nim', 'nip'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function coursesTaught(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_lecturer');
    }

    public function enrollments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attendanceSessionsOpened(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AttendanceSession::class, 'dosen_id');
    }

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function dosenWali(): BelongsTo
    {
        return $this->belongsTo(self::class, 'dosen_wali_id');
    }

    public function waliStudents(): HasMany
    {
        return $this->hasMany(self::class, 'dosen_wali_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === RoleEnum::Admin->value;
    }

    public function isDosen(): bool
    {
        return $this->role === RoleEnum::Dosen->value;
    }

    public function isMahasiswa(): bool
    {
        return $this->role === RoleEnum::Mahasiswa->value;
    }

    public function scopeRole($query, string $role): void
    {
        $query->where('role', $role);
    }

    public function scopeByJurusan($query, int $jurusanId): void
    {
        $query->where('jurusan_id', $jurusanId);
    }
}
