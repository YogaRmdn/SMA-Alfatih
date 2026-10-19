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
  - Banner (Banners)
  - Unduhan (Downloads)
  - Testimoni (Testimonials) & Mitra (Partners)
  - FAQ (Faqs) & Halaman Statis (Pages)
  - Komentar (Comments) dengan moderasi/approve
  - Manajemen PPDB (Pendaftaran Peserta Didik Baru) beserta dokumen & status pendaftaran
- Pengaturan umum (Settings) dan informasi kontak (Contact).
- Manajemen pengguna (Users) — khusus role `super_admin`.
- Autentikasi Laravel Breeze (login, registrasi, verifikasi email, reset password).

### Modul SIBEL (Sistem Informasi Gaya Belajar Siswa)
- Akses khusus di path **`/sibel`** (`routes/sibel.php`) untuk role `guru_bk`, `admin`, `super_admin`, dan `siswa`.
- Dashboard statistik + grafik distribusi gaya belajar (Chart.js dengan fallback CSS).
- CRUD Siswa & Kelas, builder Kuesioner (pertanyaan + 3 pilihan A/B/C), sesi buka/tutup, dan verifikasi instrumen.
- Pengisian kuesioner dinamis: indikator progres, konfirmasi sebelum kirim, anti kirim ganda, aturan pengisian ulang dikendalikan guru BK.
- Mesin penilaian murni di backend (`LearningStyleScoringService`) — mendukung seri 2 dan 3 kategori tanpa pemilihan acak.
- Hasil & interpretasi positif, publikasi hasil oleh guru BK sebelum siswa bisa melihat, catatan tindak lanjut guru BK.
- Laporan individual dan rekap kelas siap cetak / simpan PDF (kertas A4, `window.print()`).
- Pendaftaran mandiri siswa di **`/sibel/daftar`** (sementara menggantikan tombol "Daftar PPDB" pada navbar) — daftar → login otomatis → langsung ke pengisian kuesioner; pendaftar berlabel **"Daftar Mandiri"** + filter Asal di menu Data Siswa.
- Panduan lengkap: **[docs/SIBEL.md](docs/SIBEL.md)**.

## Catatan Keamanan

- **Jangan pernah** melakukan commit `.env`. Salin `.env.example` lalu isi sendiri.
- Set `APP_DEBUG=false` di production. Dengan `true`, halaman error membocorkan path file, query, dan isi environment variable.
- Registrasi publik di `/register` bisa dimatikan lewat `ALLOW_PUBLIC_REGISTRATION=false` (default di `.env.example`).
- Dokumen pendaftar (KK, akta, ijazah, rapor) disimpan di disk `local` (di luar `public/`) dan hanya bisa diakses lewat session pendaftar yang sudah diverifikasi — bukan lewat URL langsung.
- Admin layout mengirim `noindex, nofollow`; `public/robots.txt` juga menutup `/admin`, `/register`, dan `/ppdb/dokumen/`.
- Ganti password `superadmin@alfatih.sch.id` dan `admin@alfatih.sch.id` segera setelah seeding pertama di server.

## Teknologi

- **Laravel 12** (PHP ^8.2)
- **Laravel Breeze** untuk autentikasi
- **Blade** untuk template
- **Tailwind CSS 3** + **Vite** untuk styling & bundling
- **Alpine.js** untuk interaktivitas
- **stevebauman/purify** (HTMLPurifier) untuk sanitasi HTML konten
- **MySQL / SQLite** (database)

## Struktur Role

| Role        | Slug         | Hak Akses                                       |
|-------------|--------------|--------------------------------------------------|
| Super Admin | `super_admin`| Akses penuh, termasuk kelola pengguna            |
| Admin       | `admin`      | Mengelola seluruh konten website, termasuk SIBEL |
| Guru BK     | `guru_bk`    | Kelola data siswa/kelas/kuesioner SIBEL, hasil, publikasi & rekap |
| Siswa       | `siswa`      | Mengisi kuesioner SIBEL dan melihat hasil miliknya sendiri |

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

# 5. Set password awal akun admin (WAJIB di production)
#    Akun yang sudah ada TIDAK akan di-reset passwordnya oleh seeder.
#    Seed di .env.example:
#    SEED_ADMIN_PASSWORD=GantiDenganPasswordYangKuat

# 6. Jalankan migrasi dan seeder
php artisan migrate --seed

# 7. Instal dependensi frontend dan build
npm install
npm run build

# 8. Jalankan server
php artisan serve
```

Akses aplikasi di `http://localhost:8000`.

## Seeder

| Seeder            | Keterangan                                         |
|-------------------|-----------------------------------------------------|
| `RoleSeeder`      | Membuat role `super_admin`, `admin`, `guru_bk`, dan `siswa` |
| `SettingsSeeder`  | Data pengaturan dasar website                       |
| `DatabaseSeeder`  | Memanggil `RoleSeeder` + `SettingsSeeder` + `SibelSeeder`, lalu membuat 2 akun admin bila belum ada |
| `SibelSeeder`     | Kuesioner SIBEL bawaan (15 pertanyaan, status draft + perlu verifikasi) |
| `SibelDemoSeeder` | Data demo SIBEL (kelas, siswa, hasil) — jalankan manual dengan `php artisan db:seed --class=SibelDemoSeeder` |
| `DemoSeeder`      | Data contoh/demo konten                            |

Password akun admin diambil dari env `SEED_ADMIN_PASSWORD`:

- **Production** — wajib diisi. Kalau kosong, `db:seed` gagal dengan pesan jelas, bukan diam-diam memakai password tebakan.
- **Local/testing** — kalau kosong, password acak dicetak ke terminal.
- Akun yang email-nya sudah terdaftar **tidak** di-reset, jadi `db:seed` aman dijalankan berulang kali di server yang sudah aktif.

Setelah seeding berhasil, kosongkan lagi `SEED_ADMIN_PASSWORD` di `.env`.

## Struktur Direktori Utama

```
app/
├── Http/
│   └── Controllers/
│       ├── Admin/       # Controller panel admin
│       ├── Auth/        # Controller autentikasi (Breeze)
│       ├── Frontend/    # Controller halaman publik
│       └── Sibel/       # Controller modul SIBEL (gaya belajar)
├── Policies/            # Otorisasi (mis. SibelResultPolicy)
├── Services/            # Layanan bisnis (mis. LearningStyleScoringService)
└── Models/             # Model Eloquent
config/
└── sibel.php           # Konfigurasi kategori, warna, strategi & etika SIBEL
database/
├── migrations/         # Skema tabel
└── seeders/            # Seeder database
docs/
└── SIBEL.md            # Panduan lengkap modul SIBEL
resources/views/
├── admin/              # View panel admin
├── frontend/           # View halaman publik
├── auth/               # View autentikasi
├── sibel/              # View modul SIBEL (layout sendiri + halaman cetak A4)
└── components/         # Komponen Blade
routes/
├── web.php             # Route publik & profil
├── admin.php           # Route panel admin
├── auth.php            # Route autentikasi
└── sibel.php           # Route modul SIBEL (prefix /sibel)
```

## Lisensi

Proyek ini menggunakan lisensi **MIT**. Laravel framework juga dilisensikan di bawah [MIT license](https://opensource.org/licenses/MIT).