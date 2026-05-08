# Update & Maintenance Guide

Dokumen ini merangkum cara menjaga proyek ini tetap mudah diupdate setelah hardening terakhir, tanpa harus menebak-nebak area sensitif yang sudah diperbaiki.

## 1. Area sensitif yang wajib dicek saat update

### Attendance scan flow
File utama:
- `app/Http/Controllers/Mahasiswa/AttendanceController.php`
- `app/Http/Controllers/Api/MahasiswaController.php`
- `app/Services/QRCodeService.php`
- `app/Http/Controllers/Dosen/SessionController.php`
- `resources/views/dosen/sessions/show.blade.php`

Aturan yang sekarang wajib dipertahankan:
- QR harus memakai format `/attendance/scan/{session}?token={qr_code}`.
- Scan web dan API wajib memverifikasi bahwa token pada payload cocok dengan `attendance_sessions.qr_code`.
- Scan sukses tidak boleh mengubah `total_mahasiswa`.
- Duplicate attendance harus gagal secara aman dan konsisten.

Jika salah satu file di atas diubah, jalankan minimal:
```bash
php artisan test tests/Feature/AttendanceFlowTest.php
php artisan test tests/Feature/SecurityHardeningTest.php
```

### Auth & token flow
File utama:
- `app/Http/Controllers/Auth/LoginController.php`
- `app/Http/Controllers/Api/AuthController.php`
- `config/session.php`
- `config/sanctum.php`
- `app/Http/Middleware/RoleMiddleware.php`
- `routes/web.php`
- `routes/api.php`

Aturan yang sekarang wajib dipertahankan:
- Web login harus tetap menghasilkan session cookie `HttpOnly`.
- `SameSite` cookie harus tetap mengikuti konfigurasi app.
- Token Sanctum API harus memiliki expiry eksplisit 4 jam kecuali keputusan produk diubah.
- Boundary role admin/dosen/mahasiswa tidak boleh bocor.

Jika area ini diubah, jalankan minimal:
```bash
php artisan test tests/Feature/SecurityHardeningTest.php
```

## 2. Checklist update aman

Saat ingin mengubah fitur atau dependency, ikuti urutan ini:

1. **Baca dulu area sensitif terkait**
   - Jangan langsung refactor controller scan/auth tanpa lihat test dan service terkait.
2. **Jalankan baseline sebelum ubah apa pun**
   ```bash
   php artisan test
   npm run build
   ```
3. **Ubah sekecil mungkin**
   - Pisahkan fix logic, perubahan UI, dan perubahan docs.
4. **Jalankan test yang paling relevan dulu**
   ```bash
   php artisan test tests/Feature/AttendanceFlowTest.php
   php artisan test tests/Feature/SecurityHardeningTest.php
   ```
5. **Baru jalankan verifikasi penuh**
   ```bash
   vendor/bin/pint --dirty
   composer test
   npm run build
   ```
6. **Update dokumen jika behavior berubah**
   - `README.md`
   - `docs/production-hardening-notes.md`
   - dokumen ini

## 3. Checklist setelah pull / update branch

Setelah menarik update terbaru dari GitHub:

```bash
composer install
npm install
php artisan optimize:clear
php artisan migrate
php artisan test
npm run build
```

Jika environment baru atau storage belum siap:

```bash
php artisan storage:link
php artisan schedule:list
```

## 4. Langkah yang membuat aplikasi lebih mudah diupdate ke depan

### A. Pertahankan test sebagai pagar perubahan
Jangan ubah flow scan atau auth tanpa menambah / memperbarui test feature terkait.

### B. Hindari hardcode environment lokal
Contoh yang harus dihindari untuk commit umum:
- LAN host khusus mesin lokal
- URL lokal spesifik perangkat
- helper debug sementara seperti `_check_*.php`

### C. Pisahkan perubahan berdasarkan concern
Saat update berikutnya, usahakan commit terpisah untuk:
- hardening backend
- perubahan test
- perubahan dokumentasi
- screenshot / aset

### D. Sentralisasikan aturan bisnis bertahap
Prioritas refactor berikutnya agar update lebih mudah:
- status sesi (`aktif`, `ditutup`, `kedaluwarsa`)
- aturan scan attendance
- policy auth/token lifecycle

## 5. Verifikasi minimum sebelum push

Sebelum push perubahan ke GitHub, minimal harus lolos:

```bash
vendor/bin/pint --dirty
composer test
npm run build
```

Kalau perubahan menyentuh scan/auth, tambahkan:

```bash
php artisan test tests/Feature/AttendanceFlowTest.php
php artisan test tests/Feature/SecurityHardeningTest.php
```

## 6. Catatan keputusan saat ini

Keputusan scope saat ini:
- environment local masih acceptable untuk development
- frontend scanner via CDN masih acceptable
- session cookie sudah dibuktikan untuk `HttpOnly` dan `SameSite`
- Sanctum token dibatasi 4 jam
- abuse matrix inti sudah punya regression coverage

Jika keputusan produk berubah, update dokumen ini bersamaan dengan perubahan implementasi dan test.
