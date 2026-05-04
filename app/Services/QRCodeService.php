<?php

namespace App\Services;

use App\Models\AttendanceSession;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    public function generateQrCode(AttendanceSession $session): string
    {
        $qrData = config('app.url') . '/attendance/scan/' . $session->id . '?token=' . $this->generateToken($session);

        return QrCode::size(300)->generate($qrData);
    }

    public function verifyQrCode(string $qrData, AttendanceSession $session): bool
    {
        $parsed = parse_url($qrData);
        if (!isset($parsed['path']) || !isset($parsed['query'])) {
            return false;
        }

        preg_match('#/attendance/scan/(\d+)$#', $parsed['path'], $matches);
        if (!isset($matches[1])) {
            return false;
        }

        $sessionId = (int) $matches[1];
        parse_str($parsed['query'], $queryParams);
        $token = $queryParams['token'] ?? '';

        if ($sessionId !== $session->id) {
            return false;
        }

        return $token === $this->generateToken($session);
    }

    private function generateToken(AttendanceSession $session): string
    {
        return hash('sha256', $session->id . $session->expires_at);
    }
}
