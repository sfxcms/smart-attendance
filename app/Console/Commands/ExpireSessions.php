<?php

namespace App\Console\Commands;

use App\Models\AttendanceSession;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireSessions extends Command
{
    protected $signature = 'app:expire-sessions';

    protected $description = 'Tutup otomatis sesi absensi yang sudah melebihi waktu expired';

    public function handle(): int
    {
        $expired = AttendanceSession::where('status', 'aktif')
            ->where('expires_at', '<', Carbon::now())
            ->update(['status' => 'kedaluwarsa']);

        $this->info("Berhasil menutup {$expired} sesi kedaluwarsa.");

        return self::SUCCESS;
    }
}
