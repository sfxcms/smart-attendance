<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $ab = Jurusan::where('kode', 'AB')->first();
        $ip = Jurusan::where('kode', 'IP')->first();

        $users = [];

        $users[] = [
            'name' => 'Admin Universitas',
            'email' => 'admin@univ.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nim' => null,
            'nip' => null,
            'jurusan_id' => null,
            'dosen_wali_id' => null,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $dosen = [
            ['name' => 'Dr. Ahmad Fauzi', 'email' => 'ahmad@univ.ac.id', 'nip' => '198001012010011001', 'jurusan' => $ab],
            ['name' => 'Dr. Siti Rahmawati', 'email' => 'siti@univ.ac.id', 'nip' => '198205152005012002', 'jurusan' => $ab],
            ['name' => 'Dr. Budi Santoso', 'email' => 'budi@univ.ac.id', 'nip' => '197803102008011003', 'jurusan' => $ab],
            ['name' => 'Dr. Dewi Sartika', 'email' => 'dewi@univ.ac.id', 'nip' => '198507202010012004', 'jurusan' => $ip],
            ['name' => 'Dr. Rudi Hartono', 'email' => 'rudi@univ.ac.id', 'nip' => '197906152005011005', 'jurusan' => $ip],
            ['name' => 'Dr. Fitri Handayani', 'email' => 'fitri@univ.ac.id', 'nip' => '198312102010012006', 'jurusan' => $ip],
        ];

        foreach ($dosen as $d) {
            $users[] = [
                'name' => $d['name'],
                'email' => $d['email'],
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'nim' => null,
                'nip' => $d['nip'],
                'jurusan_id' => $d['jurusan']->id,
                'dosen_wali_id' => null,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('users')->insert($users);

        $abDosen = User::where('role', 'dosen')->where('jurusan_id', $ab->id)->get();
        $ipDosen = User::where('role', 'dosen')->where('jurusan_id', $ip->id)->get();

        $mahasiswa = [
            ['name' => 'Ahmad Wijaya', 'email' => 'ahmad.wijaya@student.univ.ac.id', 'nim' => '2201001', 'jurusan' => $ab, 'wali' => $abDosen[0]],
            ['name' => 'Bunga Lestari', 'email' => 'bunga.lestari@student.univ.ac.id', 'nim' => '2201002', 'jurusan' => $ab, 'wali' => $abDosen[0]],
            ['name' => 'Citra Dewi', 'email' => 'citra.dewi@student.univ.ac.id', 'nim' => '2201003', 'jurusan' => $ab, 'wali' => $abDosen[0]],
            ['name' => 'Dwi Prasetyo', 'email' => 'dwi.prasetyo@student.univ.ac.id', 'nim' => '2201004', 'jurusan' => $ab, 'wali' => $abDosen[1]],
            ['name' => 'Eka Putri', 'email' => 'eka.putri@student.univ.ac.id', 'nim' => '2201005', 'jurusan' => $ab, 'wali' => $abDosen[1]],
            ['name' => 'Farhan Maulana', 'email' => 'farhan.maulana@student.univ.ac.id', 'nim' => '2201006', 'jurusan' => $ab, 'wali' => $abDosen[1]],
            ['name' => 'Gina Safitri', 'email' => 'gina.safitri@student.univ.ac.id', 'nim' => '2201007', 'jurusan' => $ab, 'wali' => $abDosen[2]],
            ['name' => 'Hendra Gunawan', 'email' => 'hendra.gunawan@student.univ.ac.id', 'nim' => '2201008', 'jurusan' => $ab, 'wali' => $abDosen[2]],
            ['name' => 'Intan Permata Sari', 'email' => 'intan.permata@student.univ.ac.id', 'nim' => '2201009', 'jurusan' => $ab, 'wali' => $abDosen[2]],
            ['name' => 'Joko Firmansyah', 'email' => 'joko.firmansyah@student.univ.ac.id', 'nim' => '2201010', 'jurusan' => $ab, 'wali' => $abDosen[2]],
            ['name' => 'Kartika Sari Dewi', 'email' => 'kartika.sari@student.univ.ac.id', 'nim' => '2201011', 'jurusan' => $ip, 'wali' => $ipDosen[0]],
            ['name' => 'Lukman Hakim', 'email' => 'lukman.hakim@student.univ.ac.id', 'nim' => '2201012', 'jurusan' => $ip, 'wali' => $ipDosen[0]],
            ['name' => 'Maya Anggraini', 'email' => 'maya.anggraini@student.univ.ac.id', 'nim' => '2201013', 'jurusan' => $ip, 'wali' => $ipDosen[0]],
            ['name' => 'Nanda Pratama', 'email' => 'nanda.pratama@student.univ.ac.id', 'nim' => '2201014', 'jurusan' => $ip, 'wali' => $ipDosen[1]],
            ['name' => 'Olivia Rahmawati', 'email' => 'olivia.rahmawati@student.univ.ac.id', 'nim' => '2201015', 'jurusan' => $ip, 'wali' => $ipDosen[1]],
            ['name' => 'Putra Ramadhan', 'email' => 'putra.ramadhan@student.univ.ac.id', 'nim' => '2201016', 'jurusan' => $ip, 'wali' => $ipDosen[1]],
            ['name' => 'Rina Marliana', 'email' => 'rina.marliana@student.univ.ac.id', 'nim' => '2201017', 'jurusan' => $ip, 'wali' => $ipDosen[2]],
            ['name' => 'Satria Nugraha', 'email' => 'satria.nugraha@student.univ.ac.id', 'nim' => '2201018', 'jurusan' => $ip, 'wali' => $ipDosen[2]],
            ['name' => 'Tia Kusuma Wardhani', 'email' => 'tia.kusuma@student.univ.ac.id', 'nim' => '2201019', 'jurusan' => $ip, 'wali' => $ipDosen[2]],
            ['name' => 'Yoga Pratama', 'email' => 'yoga.pratama@student.univ.ac.id', 'nim' => '2201020', 'jurusan' => $ip, 'wali' => $ipDosen[2]],
        ];

        $mahasiswaRows = [];
        foreach ($mahasiswa as $m) {
            $mahasiswaRows[] = [
                'name' => $m['name'],
                'email' => $m['email'],
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => $m['nim'],
                'nip' => null,
                'jurusan_id' => $m['jurusan']->id,
                'dosen_wali_id' => $m['wali']->id,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('users')->insert($mahasiswaRows);
    }
}
