<?php

namespace App\Enums;

enum AttendanceStatusEnum: string
{
    case Hadir = 'hadir';
    case Izin = 'izin';
    case Sakit = 'sakit';
    case Alpha = 'alpha';
}
