# PROJECT OVERVIEW — SMA IT Tahfizh Al-Fatih

Profil ringkas struktur, stack, dan arsitektur **Website SMA IT Tahfizh Al-Fatih** (Laravel 12).

---

## Stack

| Lapisan | Teknologi |
|---|---|
| Backend | Laravel 12 · PHP ^8.2 · Laravel Breeze (auth) · `league/commonmark` |
| Frontend | Blade · Tailwind CSS 3 · Alpine.js · Vite 7 |
| Database | SQLite (default) / MySQL — session, cache, queue = database |
| Testing | PHPUnit 11 (SQLite `:memory:`) |
| Dev | `composer dev` → `scripts/dev.mjs` (server :8000, queue, pail/logs, Vite :5173) |

File kunci: `composer.json`, `package.json`, `bootstrap/app.php`, `vite.config.js`, `tailwind.config.js`, `phpunit.xml`.

---

## Struktur Direktori

```
app/
├── Http/Controllers/
│   ├── Admin/       25 controller panel admin
│   ├── Frontend/    Home, News, Ppdb
│   ├── Auth/        Breeze (9 controller)
│   └── ProfileController, Controller
├── Http/Requests/   23 FormRequest (validasi)
├── Http/Middleware/ SecurityHeaders, EnsureUserHasRole
├── Models/          26 model
├── Providers/AppServiceProvider.php   view composer $settings & $contact
├── Support/helpers.php                upload_file, delete_file, img_url, format_bytes
├── Traits/HasSlug.php                 auto-slug + route-by-slug
└── View/Components/                   AppLayout, GuestLayout

routes/      web.php | admin.php | auth.php | console.php
resources/   views/{frontend,admin,auth,components,layouts,profile}/
database/    migrations (31) | seeders (6) | factories | database.sqlite
tests/       Feature (6 auth + security + profile) | Unit (1)
scripts/     dev.mjs | tail-log.mjs
```

Tidak memakai: API routes, Livewire, Filament, Inertia, Spatie — murni server-rendered Blade.

---

## Autentikasi & Role

- Sistem role custom 2 level (tanpa Spatie): `super_admin`, `admin`
  - Tabel `roles`, kolom `users.role_id`
  - Helper `User::hasRole()`, `User::isSuperAdmin()`, `User::isAdmin()`
- Middleware alias `role` → `App\Http\Middleware\EnsureUserHasRole` (`bootstrap/app.php`)
- Route admin: `auth + verified + role:super_admin|admin`, prefix `/admin`
- **CRUD user** khusus `role:super_admin` (`routes/admin.php`)
- Keamanan
  - `SecurityHeaders` global (CSP, X-Frame-Options, dll.)
  - Rate-limit pada login/PPDB/komentar
  - Whitelist ekstensi upload di sisi server (`UPLOAD_ALLOWED_EXTENSIONS`)

---

## Database (31 migrasi)

**Inti:** `users`(+`role_id`), `roles`, `settings`, `cache`, `jobs`

**Konten:** `categories`, `news` (soft deletes), `announcements`, `teachers`, `staffs`, `albums`, `galleries`, `achievements`, `facilities`, `extracurriculars`, `programs`, `sliders`, `banners`, `downloads`, `testimonials`, `partners`, `faqs`, `pages`, `contacts`, `comments`, `visitor_logs`

**PPDB:** `ppdb` (`registration_number`, `access_code`, status enum `pending|verified|lulus_administrasi|rejected|accepted`) + `ppdb_documents`

Relasi kunci: News→Category/Author/Comments · Album→Galleries · Ppdb→PpdbDocuments · User→Role.

---

## Route

- `web.php`: home, berita (list/detail/komentar + throttle), PPDB (daftar/cek status/sukses/dokumen + throttle), profil, ubah password
- `admin.php`: prefix `/admin` — dashboard, **18 resource** (news, categories, announcements, teachers, staffs, albums, galleries, achievements, facilities, extracurriculars, programs, sliders, banners, downloads, testimonials, partners, faqs, pages), comments (index/destroy/approve), PPDB (list/show/document/status/destroy), settings, contact, users (super_admin)
- `auth.php`: Breeze (login, register, reset, verify) + throttle

---

## Modul

**Frontend:** landing page (sliders, banners, pengumuman, program, fasilitas, ekskul, prestasi, testimoni, partner, berita, galeri, kontak, tombol WA), berita + moderasi komentar, **PPDB online** (form multi-dokumen → cek status via No. Pendaftaran + TTL + access_code → streaming dokumen privat)

**Admin:** dashboard (statistik + chart PPDB 6 bulan & visitor 7 hari, logika tanggal beralih SQLite/MySQL), CRUD konten, persetujuan komentar, verifikasi/status PPDB, pengaturan (logo, favicon, sosmed, toggle PPDB, validasi embed map), kontak, user.

---

## Seeder

| Seeder | Isi |
|---|---|
| `DatabaseSeeder` | RoleSeeder + SettingsSeeder + 2 akun (`superadmin@alfatih.sch.id` / `admin@alfatih.sch.id`, password: `password`) |
| `RoleSeeder` | 2 role |
| `SettingsSeeder` | 20+ pengaturan site (ppdb_open=1, tahun ajaran 2026/2027) |
| `DemoSeeder` · `DemoContentSeeder` | Konten demo (berita, program, guru, dll.) |
| `PpdbDemoSeeder` | 6 pendaftar PPDB + dokumen fake |

---

## Detail Implementasi Penting

- `AppServiceProvider` menginjeksi `$settings` (cache 1 jam) ke semua view, `$contact` ke view frontend
- Trait `HasSlug` → routing via slug untuk News/Category/Album/Page
- PPDB: upload ke disk privat, whitelist ekstensi server-side, retry saat `registration_number` bentrok, otorisasi dokumen berbasis session
- Aturan penulisan: gaya kode mengikuti pola controller/resource yang sudah ada, `App\Support\helpers.php` untuk utilitas upload/file

---

## Testing

- `composer test` / `php artisan test`
- Cover: SecurityHeaders, Profile, 6 test auth Breeze, unit test
- Konfigurasi: SQLite `:memory:` (`phpunit.xml`)

---

## Referensi File

| Fungsi | Lokasi |
|---|---|
| Deklarasi deps & helper | `composer.json` |
| Alias middleware `role` + beban route admin | `bootstrap/app.php` |
| Route publik | `routes/web.php` |
| Route admin | `routes/admin.php` |
| Route auth | `routes/auth.php` |
| Cek role | `app/Http/Middleware/EnsureUserHasRole.php` |
| Header keamanan | `app/Http/Middleware/SecurityHeaders.php` |
| Model pengguna + role | `app/Models/User.php`, `app/Models/Role.php` |
| PPDB | `app/Models/Ppdb.php` |
| Util upload | `app/Support/helpers.php` |
| Auto-slug | `app/Traits/HasSlug.php` |
| View composer | `app/Providers/AppServiceProvider.php` |
| Akun seeder | `database/seeders/DatabaseSeeder.php` |
| Pengaturan seeder | `database/seeders/SettingsSeeder.php` |
| Menu sidebar admin | `resources/views/admin/layouts/app.blade.php` |
| Layout publik | `resources/views/frontend/layouts/app.blade.php` |
| Runner dev | `scripts/dev.mjs` |