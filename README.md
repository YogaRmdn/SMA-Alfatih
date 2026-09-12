<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<h1 align="center">Website SMA IT Tahfizh Al-Fatih</h1>

<p align="center">
  Website profil sekolah berbasis <strong>Laravel 12</strong> dengan Halaman Depan (Frontend) dan Panel Admin lengkap.
</p>

## Fitur Utama

### Halaman Depan (Publik)
- Landing page profil sekolah dengan slider/banner, pengumuman, program, fasilitas, ekstrakurikuler, prestasi, testimoni, galeri foto, dan berita terbaru.
- Beranda menampilkan statistik guru, prestasi, ekstrakurikuler, dan siswa.

### Panel Admin
- Dashboard dengan statistik dan ringkasan data.
- Manajemen konten:
  - Berita (News) & Kategori (Categories)
  - Pengumuman (Announcements)
  - Guru (Teachers) & Staf (Staffs)
  - Album & Galeri Foto (Albums, Galleries)
  - Prestasi (Achievements)
  - Fasilitas (Facilities) & Ekstrakurikuler (Extracurriculars)
  - Program (Programs)
  - Slider & Banner (Sliders, Banners)
  - Unduhan (Downloads)
  - Testimoni (Testimonials) & Mitra (Partners)
  - FAQ (Faqs) & Halaman Statis (Pages)
  - Komentar (Comments) dengan moderasi/approve
  - Manajemen PPDB (Pendaftaran Peserta Didik Baru) beserta dokumen & status pendaftaran
- Pengaturan umum (Settings) dan informasi kontak (Contact).
- Manajemen pengguna (Users) — khusus role `super_admin`.
- Autentikasi Laravel Breeze (login, registrasi, verifikasi email, reset password).

## Teknologi

- **Laravel 12** (PHP ^8.2)
- **Laravel Breeze** untuk autentikasi
- **Blade** untuk template
- **Tailwind CSS** + **Vite** untuk styling & bundling
- **Alpine.js** untuk interaktivitas
- **MySQL / SQLite** (database)

## Struktur Role

| Role        | Slug         | Hak Akses                                       |
|-------------|--------------|--------------------------------------------------|
| Super Admin | `super_admin`| Akses penuh, termasuk kelola pengguna            |
| Admin       | `admin`      | Mengelola seluruh konten website                 |

## Instalasi

### Prasyarat
- PHP ^8.2
- Composer
- Node.js & NPM
- MySQL (atau SQLite)

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/YogaRmdn/SMA-Alfatih.git
cd SMA-Alfatih

# 2. Instal dependensi PHP
composer install

# 3. Salin file env dan generate key
copy .env.example .env   # Windows
# cp .env.example .env   # Linux/macOS
php artisan key:generate

# 4. Konfigurasi database pada file .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 5. Jalankan migrasi dan seeder
php artisan migrate --seed

# 6. Instal dependensi frontend dan build
npm install
npm run build

# 7. Jalankan server
php artisan serve
```

Akses aplikasi di `http://localhost:8000`.

## Seeder

| Seeder            | Keterangan                                         |
|-------------------|-----------------------------------------------------|
| `RoleSeeder`      | Membuat role `super_admin` dan `admin`              |
| `SettingsSeeder`  | Data pengaturan dasar website                       |
| `DemoSeeder`      | Data contoh/demo konten                            |

## Struktur Direktori Utama

```
app/
├── Http/
│   └── Controllers/
│       ├── Admin/       # Controller panel admin
│       ├── Auth/        # Controller autentikasi (Breeze)
│       └── Frontend/    # Controller halaman publik
└── Models/             # Model Eloquent
database/
├── migrations/         # Skema tabel
└── seeders/            # Seeder database
resources/views/
├── admin/              # View panel admin
├── frontend/           # View halaman publik
├── auth/               # View autentikasi
└── components/         # Komponen Blade
routes/
├── web.php             # Route publik & profil
├── admin.php           # Route panel admin
└── auth.php            # Route autentikasi
```

## Lisensi

Proyek ini menggunakan lisensi **MIT**. Laravel framework juga dilisensikan di bawah [MIT license](https://opensource.org/licenses/MIT).