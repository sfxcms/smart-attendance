<?php

namespace App\Models;

use App\Enums\SessionTipeEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['schedule_id', 'course_id', 'dosen_id', 'qr_code', 'expires_at', 'status', 'total_mahasiswa', 'tipe_sesi', 'link_meeting'])]
class AttendanceSession extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'tipe_sesi' => SessionTipeEnum::class,
        ];
    }

    public function isOnline(): bool
    {
        return $this->tipe_sesi === SessionTipeEnum::Online;
    }

    public function isOffline(): bool
    {
        return $this->tipe_sesi === SessionTipeEnum::Offline;
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
