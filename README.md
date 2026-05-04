# Smart Attendance System

<div align="center">

<!-- Hero Banner SVG -->
<img src="https://capsule-render.vercel.app/api?type=rect&color=0:4F46E5,100:7C3AED&height=300&section=header&text=Smart%20Attendance&desc=Sistem%20Absensi%20Hybrid%20Online%20%2B%20Offline&fontSize=60&fontColor=ffffff&animation=scaleIn" width="100%" alt="Smart Attendance Banner"/>

<p><strong>Sistem Absensi Hybrid (Online + Offline) untuk Perkuliahan</strong></p>
<p><em>Hybrid Attendance System for University Classes — built with Laravel, QR Codes, and real-time analytics.</em></p>

[![Laravel](https://img.shields.io/badge/Laravel-13.x-red?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-4.x-06B6D4?style=for-the-badge&logo=tailwindcss)](https://tailwindcss.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15+-4169E1?style=for-the-badge&logo=postgresql)](https://postgresql.org)

</div>

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Quick Start (One-Liner Install)](#quick-start-one-liner-install)
- [Manual Installation](#manual-installation)
- [Configuration](#configuration)
- [Usage Guide](#usage-guide)
  - [Admin](#admin)
  - [Dosen (Lecturer)](#dosen-lecturer)
  - [Mahasiswa (Student)](#mahasiswa-student)
- [API Documentation](#api-documentation)
- [Screenshots](#screenshots)
- [Development](#development)
- [Contributing](#contributing)
- [License](#license)

---

## Features

### Hybrid Attendance
- **Offline Mode**: Dosen displays a dynamic QR code on the projector. Students scan using their phone camera.
- **Online Mode**: Dosen creates a session with a meeting link (Zoom/Google Meet). Students can join via the provided link.
- **Auto-Expiry**: Sessions automatically close after 15 minutes or via scheduled command.

### Multi-Role System
- **Admin**: Full dashboard, analytics, manage users, courses, schedules, and enrollments.
- **Dosen**: Create sessions, view real-time attendance, export CSV, manage student attendance records.
- **Mahasiswa**: Scan QR for offline classes, join meeting links for online classes, view attendance history.

### Real-Time Analytics
- Attendance trends by department (Jurusan)
- Attendance by semester
- 30-day attendance trend charts
- Course-level attendance rates

### REST API
- Token-based authentication (Laravel Sanctum)
- Endpoints for mobile app integration
- Rate-limited endpoints for production use

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 13.x (PHP 8.3+) |
| Frontend | Blade + Tailwind CSS 4.x + Vite |
| Database | PostgreSQL 15+ |
| Auth | Laravel Sanctum (API) + Session (Web) |
| QR Scanner | html5-qrcode (browser-based) |
| Charts | Chart.js 4.x |
| Queue | Database (default) |

---

## Quick Start (One-Liner Install)

### Requirements

- PHP 8.3+ with extensions: `pgsql`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`
- PostgreSQL 15+
- Composer 2.x
- Node.js 20+ & npm 10+
- Git

### One-Liner

```bash
curl -sSL https://raw.githubusercontent.com/sfxcms/smart-attendance/main/install.sh | bash
```

**Or step-by-step:**

```bash
git clone https://github.com/sfxcms/smart-attendance.git
cd smart-attendance
php install.php
```

The `install.php` script will:
1. Check PHP & PostgreSQL versions
2. Install Composer dependencies
3. Install npm dependencies & build assets
4. Create `.env` from `.env.example`
5. Generate application key
6. Run migrations & seeders
7. Create the first admin account
8. Print login credentials

---

## Manual Installation

### 1. Clone & Enter Directory

```bash
git clone https://github.com/sfxcms/smart-attendance.git
cd smart-attendance
```

### 2. Install PHP Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

For development:
```bash
composer install
```

### 3. Install Frontend Dependencies & Build

```bash
npm install
npm run build
```

For development with hot reload:
```bash
npm run dev
```

### 4. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smart_attendance
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 5. Database Setup

```bash
# Create PostgreSQL database
createdb smart_attendance

# Run migrations
php artisan migrate

# (Optional) Seed demo data
php artisan db:seed
```

### 6. Storage Link

```bash
php artisan storage:link
```

### 7. Queue Worker (for production)

```bash
php artisan queue:work
```

### 8. Schedule (Auto-Expire Sessions)

Add to your server's crontab:

```bash
* * * * * cd /path/to/smart-attendance && php artisan schedule:run >> /dev/null 2>&1
```

Or run locally:
```bash
php artisan schedule:work
```

### 9. Serve Application

```bash
php artisan serve
```

Access at `http://localhost:8000`

---

## Configuration

### Default Admin Account

If you seeded the database, the default admin is:

| Field | Value |
|-------|-------|
| Email | `admin@university.ac.id` |
| Password | `password` |

> Change this immediately after first login.

### Session Expiry

Sessions expire after **15 minutes** by default. Adjust in `app/Http/Controllers/Dosen/SessionController.php`:

```php
'expires_at' => now()->addMinutes(15), // Change 15 to your preference
```

### API Rate Limiting

API endpoints are rate-limited. Adjust in `routes/api.php`:

```php
->middleware('throttle:60,1') // 60 requests per minute
```

---

## Usage Guide

### Admin

After logging in as admin, you can:

1. **Dashboard** — View overall statistics and charts
2. **Mata Kuliah (Courses)** — Manage courses and assign lecturers
3. **Jurusan (Departments)** — Manage academic departments
4. **Jadwal (Schedules)** — Create class schedules (day, time, room)
5. **Pengguna (Users)** — Create/edit admin, dosen, and mahasiswa accounts
6. **Pendaftaran (Enrollments)** — Enroll students into departments/semesters
7. **Mahasiswa** — View student enrollment status
8. **Analitik** — Full attendance analytics with charts

### Dosen (Lecturer)

1. **Login** — Access your lecturer dashboard
2. **Dashboard** — See today's schedule and active sessions
3. **Buat Sesi** — Choose a schedule, select mode:
   - **Offline**: Displays QR code for students to scan
   - **Online**: Provide meeting link (Zoom/Google Meet)
4. **Lihat Sesi** — Monitor real-time attendance, export to CSV
5. **Tutup Sesi** — Close the session manually
6. **Statistik Kehadiran** — View student attendance statistics

### Mahasiswa (Student)

1. **Login** — See today's class schedule
2. **Scan QR** — For offline sessions:
   - Allow camera access
   - Scan the QR code displayed by the dosen
   - Or enter the session code manually
3. **Online Sessions** — For online sessions:
   - Click the **Buka Meeting** link directly
   - Attendance is recorded automatically
4. **Riwayat** — View full attendance history with filters (course, month, status)

---

## API Documentation

### Authentication

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "mahasiswa@example.com",
  "password": "password"
}
```

Response:
```json
{
  "success": true,
  "token": "1|xxxxxxxxxxxxxxxxxxxx",
  "user": { ... }
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

### Mahasiswa Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/schedules` | Today's enrolled schedules |
| POST | `/api/attendance/scan` | Scan QR code |
| GET | `/api/attendance/history` | Attendance history |

### Dosen Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/dosen/sessions` | List sessions |
| POST | `/api/dosen/sessions` | Create new session |
| GET | `/api/dosen/sessions/{id}/attendance` | Session attendance |
| POST | `/api/dosen/sessions/{id}/close` | Close session |
| GET | `/api/dosen/courses` | My courses |
| GET | `/api/dosen/courses/{id}/schedules` | Course schedules |

### Scan QR (Example)

```http
POST /api/attendance/scan
Authorization: Bearer {token}
Content-Type: application/json

{
  "qr_data": "http://localhost/attendance/scan/1?token=qr_..."
}
```

---

## Screenshots

### 👑 Admin Panel

| Dashboard | Analytics | Manajemen Mata Kuliah |
|:----------:|:----------:|:---------------------:|
| ![Admin Dashboard](docs/screenshots/admin-dashboard.png) | ![Analytics](docs/screenshots/admin-analytics.png) | ![Courses](docs/screenshots/admin-courses.png) |

### 👨‍🏫 Dosen (Lecturer) Panel

| Dashboard | Buat Sesi (Offline/Online) | Daftar Sesi |
|:----------:|:--------------------------:|:-----------:|
| ![Dosen Dashboard](docs/screenshots/dosen-dashboard.png) | ![Buat Sesi](docs/screenshots/dosen-create-session.png) | ![Sesi](docs/screenshots/dosen-sessions.png) |

### 👨‍🎓 Mahasiswa (Student) Panel

| Dashboard | Scan QR / Online | Riwayat Kehadiran |
|:----------:|:----------------:|:-----------------:|
| ![Mahasiswa Dashboard](docs/screenshots/mahasiswa-dashboard.png) | ![Scan](docs/screenshots/mahasiswa-scan.png) | ![History](docs/screenshots/mahasiswa-history.png) |

### 🔐 Login Page

![Login Page](docs/screenshots/login-page.png)

---

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

This project follows PSR-12 coding standards.

```bash
vendor/bin/pint
```

### Useful Commands

```bash
# Clear all caches
php artisan optimize:clear

# Check expired sessions manually
php artisan app:expire-sessions

# Database refresh with seeders
php artisan migrate:fresh --seed
```

---

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Please make sure to update tests as appropriate.

---

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

## Acknowledgments

- Built with [Laravel](https://laravel.com)
- Styled with [Tailwind CSS](https://tailwindcss.com)
- QR scanning powered by [html5-qrcode](https://github.com/mebjas/html5-qrcode)
- Charts by [Chart.js](https://www.chartjs.org)

---

## Support

<div align="center">

⭐ If you find this project helpful, please give it a star on GitHub!

[![GitHub stars](https://img.shields.io/github/stars/sfxcms/smart-attendance?style=social)](https://github.com/sfxcms/smart-attendance)
[![GitHub forks](https://img.shields.io/github/forks/sfxcms/smart-attendance?style=social)](https://github.com/sfxcms/smart-attendance)
[![GitHub issues](https://img.shields.io/github/issues/sfxcms/smart-attendance?style=social)](https://github.com/sfxcms/smart-attendance/issues)

For issues and feature requests, please use the [GitHub Issues](https://github.com/sfxcms/smart-attendance/issues) page.

</div>
