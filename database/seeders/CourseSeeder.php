<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $ab = Jurusan::where('kode', 'AB')->first();
        $ip = Jurusan::where('kode', 'IP')->first();

        $courses = [
            // Administrasi Bisnis (AB) — kode SAB
            ['kode_mk' => 'SAB1101', 'nama_mk' => 'Pengantar Bisnis', 'sks' => 3, 'semester' => 1, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1102', 'nama_mk' => 'Matematika Ekonomi', 'sks' => 3, 'semester' => 1, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1103', 'nama_mk' => 'Pengantar Manajemen', 'sks' => 3, 'semester' => 1, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1104', 'nama_mk' => 'Pengantar Akuntansi', 'sks' => 3, 'semester' => 1, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1105', 'nama_mk' => 'Bahasa Indonesia', 'sks' => 2, 'semester' => 1, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1201', 'nama_mk' => 'Ekonomi Mikro', 'sks' => 3, 'semester' => 2, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1202', 'nama_mk' => 'Statistika Bisnis', 'sks' => 3, 'semester' => 2, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1203', 'nama_mk' => 'Hukum Bisnis', 'sks' => 2, 'semester' => 2, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1204', 'nama_mk' => 'Manajemen SDM', 'sks' => 3, 'semester' => 2, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1205', 'nama_mk' => 'Komunikasi Bisnis', 'sks' => 2, 'semester' => 2, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1301', 'nama_mk' => 'Ekonomi Makro', 'sks' => 3, 'semester' => 3, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1302', 'nama_mk' => 'Manajemen Keuangan', 'sks' => 3, 'semester' => 3, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1303', 'nama_mk' => 'Manajemen Pemasaran', 'sks' => 3, 'semester' => 3, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1304', 'nama_mk' => 'Sistem Informasi Manajemen', 'sks' => 3, 'semester' => 3, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1305', 'nama_mk' => 'Perilaku Organisasi', 'sks' => 3, 'semester' => 3, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1401', 'nama_mk' => 'Manajemen Operasi', 'sks' => 3, 'semester' => 4, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1402', 'nama_mk' => 'Akuntansi Manajemen', 'sks' => 3, 'semester' => 4, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1403', 'nama_mk' => 'Metode Kuantitatif', 'sks' => 3, 'semester' => 4, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1404', 'nama_mk' => 'Bisnis Digital', 'sks' => 2, 'semester' => 4, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1405', 'nama_mk' => 'Etika Bisnis', 'sks' => 2, 'semester' => 4, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1501', 'nama_mk' => 'Manajemen Strategik', 'sks' => 3, 'semester' => 5, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1502', 'nama_mk' => 'Pasar Modal', 'sks' => 3, 'semester' => 5, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1503', 'nama_mk' => 'Kewirausahaan', 'sks' => 2, 'semester' => 5, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1504', 'nama_mk' => 'Bisnis Internasional', 'sks' => 3, 'semester' => 5, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1505', 'nama_mk' => 'Manajemen Risiko', 'sks' => 3, 'semester' => 5, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB2161', 'nama_mk' => 'Metode Penelitian Bisnis', 'sks' => 3, 'semester' => 6, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB2162', 'nama_mk' => 'Studi Kelayakan Bisnis', 'sks' => 3, 'semester' => 6, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB2163', 'nama_mk' => 'Perilaku Keorganisasian', 'sks' => 3, 'semester' => 6, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB2164', 'nama_mk' => 'Sustainable Business', 'sks' => 2, 'semester' => 6, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB2165', 'nama_mk' => 'Manajemen Logistik', 'sks' => 3, 'semester' => 6, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1701', 'nama_mk' => 'Seminar Proposal', 'sks' => 2, 'semester' => 7, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1702', 'nama_mk' => 'Manajemen Perubahan', 'sks' => 3, 'semester' => 7, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1703', 'nama_mk' => 'Bisnis Kreatif', 'sks' => 2, 'semester' => 7, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1704', 'nama_mk' => 'Analisis Laporan Keuangan', 'sks' => 3, 'semester' => 7, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1705', 'nama_mk' => 'Perpajakan', 'sks' => 2, 'semester' => 7, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1801', 'nama_mk' => 'KKN/Tugas Akhir', 'sks' => 4, 'semester' => 8, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1802', 'nama_mk' => 'Etika Profesi', 'sks' => 2, 'semester' => 8, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1803', 'nama_mk' => 'Kepemimpinan', 'sks' => 2, 'semester' => 8, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1804', 'nama_mk' => 'Negosiasi Bisnis', 'sks' => 2, 'semester' => 8, 'jurusan_id' => $ab->id],
            ['kode_mk' => 'SAB1805', 'nama_mk' => 'Manajemen Kualitas', 'sks' => 3, 'semester' => 8, 'jurusan_id' => $ab->id],

            // Ilmu Pemerintahan (IP) — kode SIP
            ['kode_mk' => 'SIP1101', 'nama_mk' => 'Pengantar Ilmu Pemerintahan', 'sks' => 3, 'semester' => 1, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1102', 'nama_mk' => 'Pengantar Sosiologi', 'sks' => 3, 'semester' => 1, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1103', 'nama_mk' => 'Pengantar Hukum Indonesia', 'sks' => 3, 'semester' => 1, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1104', 'nama_mk' => 'Bahasa Indonesia', 'sks' => 2, 'semester' => 1, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1105', 'nama_mk' => 'Pancasila', 'sks' => 2, 'semester' => 1, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1201', 'nama_mk' => 'Teori Pemerintahan', 'sks' => 3, 'semester' => 2, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1202', 'nama_mk' => 'Sistem Politik Indonesia', 'sks' => 3, 'semester' => 2, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1203', 'nama_mk' => 'Hukum Tata Negara', 'sks' => 3, 'semester' => 2, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1204', 'nama_mk' => 'Statistik Sosial', 'sks' => 2, 'semester' => 2, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1205', 'nama_mk' => 'Komunikasi Politik', 'sks' => 2, 'semester' => 2, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1301', 'nama_mk' => 'Otonomi Daerah', 'sks' => 3, 'semester' => 3, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1302', 'nama_mk' => 'Kebijakan Publik', 'sks' => 3, 'semester' => 3, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1303', 'nama_mk' => 'Administrasi Publik', 'sks' => 3, 'semester' => 3, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1304', 'nama_mk' => 'Sistem Pemerintahan Daerah', 'sks' => 3, 'semester' => 3, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1305', 'nama_mk' => 'Sosiologi Politik', 'sks' => 2, 'semester' => 3, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1401', 'nama_mk' => 'Keuangan Negara', 'sks' => 3, 'semester' => 4, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1402', 'nama_mk' => 'Manajemen Publik', 'sks' => 3, 'semester' => 4, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1403', 'nama_mk' => 'Pelayanan Publik', 'sks' => 3, 'semester' => 4, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1404', 'nama_mk' => 'Good Governance', 'sks' => 2, 'semester' => 4, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1405', 'nama_mk' => 'Perbandingan Pemerintahan', 'sks' => 3, 'semester' => 4, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1501', 'nama_mk' => 'Kepemimpinan Pemerintahan', 'sks' => 3, 'semester' => 5, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1502', 'nama_mk' => 'Partai Politik', 'sks' => 2, 'semester' => 5, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1503', 'nama_mk' => 'Hukum Administrasi Negara', 'sks' => 3, 'semester' => 5, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1504', 'nama_mk' => 'Pengawasan Publik', 'sks' => 2, 'semester' => 5, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1505', 'nama_mk' => 'Etika Pemerintahan', 'sks' => 2, 'semester' => 5, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1601', 'nama_mk' => 'Metode Penelitian Sosial', 'sks' => 3, 'semester' => 6, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1602', 'nama_mk' => 'Analisis Kebijakan', 'sks' => 3, 'semester' => 6, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1603', 'nama_mk' => 'Pemerintahan Digital', 'sks' => 3, 'semester' => 6, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1604', 'nama_mk' => 'Manajemen Konflik', 'sks' => 2, 'semester' => 6, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1605', 'nama_mk' => 'Pembangunan Daerah', 'sks' => 3, 'semester' => 6, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1701', 'nama_mk' => 'Seminar Proposal', 'sks' => 2, 'semester' => 7, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1702', 'nama_mk' => 'Reformasi Birokrasi', 'sks' => 3, 'semester' => 7, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1703', 'nama_mk' => 'Diplomasi Indonesia', 'sks' => 2, 'semester' => 7, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1704', 'nama_mk' => 'Gender dan Pemerintahan', 'sks' => 2, 'semester' => 7, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1705', 'nama_mk' => 'CSR dan Pemerintahan', 'sks' => 2, 'semester' => 7, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1801', 'nama_mk' => 'KKN/Tugas Akhir', 'sks' => 4, 'semester' => 8, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1802', 'nama_mk' => 'Hukum Pemilu', 'sks' => 2, 'semester' => 8, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1803', 'nama_mk' => 'Pemerintahan Desa', 'sks' => 3, 'semester' => 8, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1804', 'nama_mk' => 'Isu-isu Global', 'sks' => 2, 'semester' => 8, 'jurusan_id' => $ip->id],
            ['kode_mk' => 'SIP1805', 'nama_mk' => 'Perencanaan Pembangunan', 'sks' => 3, 'semester' => 8, 'jurusan_id' => $ip->id],
        ];

        $rows = [];
        foreach ($courses as $c) {
            $rows[] = [
                'kode_mk' => $c['kode_mk'],
                'nama_mk' => $c['nama_mk'],
                'sks' => $c['sks'],
                'semester' => $c['semester'],
                'jurusan_id' => $c['jurusan_id'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('courses')->insert($rows);
    }
}
