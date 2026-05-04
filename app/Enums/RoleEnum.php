<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Admin = 'admin';
    case Dosen = 'dosen';
    case Mahasiswa = 'mahasiswa';
}
