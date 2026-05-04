<?php

namespace App\Enums;

enum SessionStatusEnum: string
{
    case Aktif = 'aktif';
    case Ditutup = 'ditutup';
    case Kedaluwarsa = 'kedaluwarsa';
}
