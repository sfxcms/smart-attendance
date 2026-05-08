# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- Added `docs/update-maintenance-guide.md` to make future updates safer and more repeatable.
- Added `tests/Feature/AttendanceFlowTest.php` coverage for QR attendance payload compatibility and student scan regressions.
- Added `tests/Feature/SecurityHardeningTest.php` coverage for session cookie behavior, Sanctum token expiry, and core abuse-matrix access boundaries.
- Added GitHub Actions CI workflow for Pint, backend tests, and frontend build verification.

### Changed
- Aligned QR attendance payloads to the mahasiswa scan route format: `/attendance/scan/{session}?token={qr_code}`.
- Enforced server-side QR token validation in both web and API attendance scan flows.
- Stopped mutating `total_mahasiswa` during student attendance scans.
- Improved duplicate attendance handling so DB constraint collisions return safe duplicate-scan responses instead of crashing.
- Updated dosen session views and filters to handle `kedaluwarsa` status explicitly.
- Set Sanctum API tokens to expire after 4 hours by default and persisted that expiration at login time.
- Refreshed production hardening notes and README maintenance references.

### Security
- Hardened attendance trust boundaries so valid session IDs alone are no longer enough; the issued QR token must also match.
- Added automated proof that the session cookie is issued with `HttpOnly` and configured `SameSite` behavior in the current local web login flow.
- Added regression coverage for role-based abuse cases across web and API surfaces.

### Maintenance
- Ignored local debug/check artifacts and generic local image/snapshot residue without ignoring committed documentation screenshots.
