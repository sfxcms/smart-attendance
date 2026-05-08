# Production Hardening Notes

## Perbaikan yang sudah dilakukan
- Payload QR pada halaman sesi dosen disejajarkan ke rute scan mahasiswa: `/attendance/scan/{session}?token={qr_code}`.
- Scan absensi mahasiswa via web tidak lagi menaikkan `total_mahasiswa`, sehingga total peserta terdaftar tetap akurat.
- Parser QR web sekarang menerima pola payload scan yang sama dengan API.
- Halaman sesi dosen sekarang menampilkan link scan cadangan untuk fallback manual saat QR sulit dipindai.
- Filter dan respons sesi dosen kini mengenali status `kedaluwarsa` agar tidak salah dilabeli sebagai `ditutup`.
- Scan web dan API sekarang memverifikasi pasangan `session_id + qr_code` di server sebelum membuat absensi.
- Duplicate attendance yang terpukul race condition sekarang ditangani aman dengan fallback ke respons duplicate yang konsisten.
- Token Sanctum hasil login API sekarang memiliki masa berlaku default 4 jam dan tersimpan eksplisit di `expires_at`.
- Session cookie web telah dibuktikan lewat test otomatis membawa flag `HttpOnly` dan `SameSite` sesuai konfigurasi aplikasi.

## Bug/risiko yang masih perlu pengembangan lanjutan
### 1. Konsistensi status sesi masih bisa dipusatkan
Status sesi sudah memakai `aktif`, `ditutup`, dan `kedaluwarsa`, tetapi pengecekan masih tersebar di beberapa controller.
- Dampak: rawan mismatch perilaku saat aturan status berubah.
- Rekomendasi: pindahkan rule status ke model/service/helper terpusat atau gunakan enum cast penuh di model.

### 2. Cakupan test masih perlu diperluas
Saat ini regression test sudah mencakup flow QR/scan utama, pembuktian session cookie, expiry token API, dan abuse matrix inti; namun cakupan belum menyentuh seluruh surface aplikasi.
- Dampak: area close session web, online meeting flow, export CSV, analytics, dan operasi admin lain masih rawan regresi.
- Rekomendasi: tambah feature test untuk create/close session dosen, login/logout API expiry path, route admin utama, dan flow online session.

### 3. Environment produksi masih perlu hardening operasional
Dari baseline aplikasi:
- `APP_DEBUG` masih aktif di environment lokal saat saya cek.
- `public/storage` belum ter-link.
- schedule/queue perlu dipastikan berjalan di server produksi.
- Rekomendasi: siapkan checklist deploy yang memaksa `APP_ENV=production`, `APP_DEBUG=false`, `php artisan storage:link`, scheduler, queue worker/supervisor, dan config/route/view cache sesuai kebutuhan deploy.

### 4. Proteksi observability dan error reporting belum terlihat kuat
Belum tampak jalur logging/monitoring khusus untuk kegagalan scan, expiry, atau auth abuse.
- Dampak: incident produksi akan lebih sulit ditelusuri.
- Rekomendasi: tambahkan logging terstruktur untuk scan gagal, penutupan sesi, dan login gagal berulang; pertimbangkan integrasi monitoring eksternal.

### 5. Multi-device token policy API perlu diputuskan
Login API saat ini menghapus semua token lama setiap kali user login.
- Dampak: sesi device lain akan terputus otomatis.
- Rekomendasi: tetapkan apakah ini memang kebijakan produk. Jika tidak, ganti ke revokasi token per-device atau device-aware token naming.

## Prioritas pengembangan berikutnya
1. Rapikan sentralisasi status sesi dan aturan bisnis absensi.
2. Tambah test regresi untuk create/close session, expiry token, export, dan flow admin utama.
3. Buat checklist deploy produksi + automasi CI untuk `test`, `pint`, dan `build`.
4. Tambah logging dan monitoring untuk flow absensi penting.
5. Putuskan kebijakan multi-device token API lalu sesuaikan implementasinya.
