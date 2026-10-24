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
- Akses siswa di path **`/sibel`** (`routes/sibel.php`) **tanpa login** — cukup tulis nama + pilih kelas di portal, lalu jawab kuesioner dalam satu halaman (identitas terikat ke session browser).
- Area pengelola (`guru_bk`, `admin_sibel`, `admin`, `super_admin`) tetap lewat login website (`/login`).
- Dashboard statistik + grafik distribusi gaya belajar (Chart.js dengan fallback CSS).
- CRUD Siswa & Kelas, builder Kuesioner (pertanyaan + 3 pilihan A/B/C), sesi buka/tutup, dan verifikasi instrumen.
- Pengisian kuesioner dinamis: indikator progres, konfirmasi sebelum kirim, anti kirim ganda, aturan pengisian ulang dikendalikan guru BK.
- Mesin penilaian murni di backend (`LearningStyleScoringService`) — mendukung seri 2 dan 3 kategori tanpa pemilihan acak.
- **Hasil langsung tampil & bisa diunduh/dicetak siswa** begitu jawaban dikirim, lengkap dengan interpretasi, rekomendasi, dan catatan tindak lanjut guru BK.
- Laporan individual dan rekap kelas siap cetak / simpan PDF (kertas A4, `window.print()`).
- Tombol **"Isi untuk Nama Lain"** di topbar & halaman hasil — melepas ikatan identitas di komputer bersama.
- Panduan lengkap: **[docs/SIBEL.md](docs/SIBEL.md)**.

### Modul TOEFL ITP (Ujian Online)
- Akses peserta di path **`/toefl`** (`routes/toefl.php`) **TANPA registrasi & login** — peserta menulis **nama lengkap + kelas**, memilih ujian, lalu memasukkan **kode akses** (alur sama seperti SIBEL). Identitas & sesi terikat ke session browser; **guard `toefl` dan role `toefl_siswa` sudah dihapus**. Kode akses bersifat **level ujian** (diterbitkan pengelola di halaman ujian, hash SHA-256, plaintext tampil sekali).
- 3 bagian resmi TOEFL ITP Level 1 — Listening (50 soal/35 mnt), Structure (40/25 mnt), Reading (50/55 mnt) — jumlah & durasi **konfigurabel per ujian**.
- Timer dihitung dari **timestamp server** (grace 30 detik), auto-save jawaban, satu sesi per peserta + ujian (koneksi putus → tulis **nama + kelas & kode yang sama** di portal → sesi berlanjut).
- Audio Listening **maksimal 2 putar** per aset — dicatat server sebelum URL stream (signed, 10 menit) diterbitkan; file di disk privat.
- Kunci jawaban & penilaian **hanya di server**; skor scaled 310–680 (aproksimasi latihan, bukan skor resmi ETS).
- Pencatatan peristiwa browser (pindah tab / keluar layar penuh) sebagai catatan pendukung.
- Panel pengelola: dashboard, kelola ujian/soal/bacaan/audio, kode akses per ujian (hash SHA-256, plaintext tampil sekali), data peserta, pantau sesi (selesaikan/buka kembali), hasil + ekspor XLSX.
- **Data dummy siap demo**: `php artisan db:seed --class=ToeflDummySeeder` membuat 3 ujian (ditutup / dibuka / draf) dengan **2 bank soal penuh (140 butir per ujian)**, 30 identitas peserta, 28 hasil terscore, dan 3 sesi berjalan — semua tanpa akun login. Butir soal ada di `database/data/toefl/`.
- **Tombol "Masuk sebagai Peserta Demo"** di portal `/toefl` untuk langsung melihat tampilan pengerjaan tanpa mengetik kode akses. **Hanya tampil & berfungsi saat `APP_DEBUG=true`** — routenya sendiri `404` begitu debug mati, sehingga jalur melewati kode tidak pernah ada di produksi.
- Panduan lengkap: **[docs/TOEFL.md](docs/TOEFL.md)**.

## Catatan Keamanan

- **Jangan pernah** melakukan commit `.env`. Salin `.env.example` lalu isi sendiri.
- Set `APP_DEBUG=false` di production. Dengan `true`, halaman error membocorkan path file, query, dan isi environment variable.
- Registrasi publik di `/register` bisa dimatikan lewat `ALLOW_PUBLIC_REGISTRATION=false` (default di `.env.example`).
- Dokumen pendaftar (KK, akta, ijazah, rapor) disimpan di disk `local` (di luar `public/`) dan hanya bisa diakses lewat session pendaftar yang sudah diverifikasi — bukan lewat URL langsung.
- Kode akses TOEFL disimpan hanya sebagai hash SHA-256 (plaintext tampil satu kali saat diterbitkan di halaman ujian), dan audio ujian berada di disk privat — hanya bisa diputar lewat signed URL yang cocok dengan session attempt peserta (maks. 2 putar). Modul TOEFL **tidak punya akun/kata sandi peserta** — kunci masuknya adalah kode akses + nama/kelas yang ditulis peserta.
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
| Admin       | `admin`      | Mengelola seluruh konten website, termasuk SIBEL & TOEFL |
| Guru BK     | `guru_bk`    | **Khusus modul SIBEL** — data siswa/kelas/kuesioner, hasil & rekap; **tidak** bisa membuka panel TOEFL (`/toefl/kelola`) |
| Admin SIBEL | `admin_sibel`| **Khusus modul SIBEL** — akses penuh ke `/sibel` saja; **tidak** bisa membuka panel website (`/admin`) maupun panel TOEFL (`/toefl/kelola`) |
| Admin TOEFL | `admin_toefl`| **Khusus modul TOEFL** — ujian, soal, kode akses, data peserta, hasil & ekspor di `/toefl/kelola`; **tidak** bisa membuka panel website (`/admin`) maupun SIBEL (`/sibel`) |
| Siswa       | `siswa`      | Tidak dipakai untuk SIBEL lagi — siswa mengisi kuesioner tanpa akun (tulis nama + pilih kelas di `/sibel`); akun lama tetap bisa login website |

> SIBEL dan TOEFL sengaja **dipisah sampai ke level akun**: pengelola satu
> modul tidak melihat menu, route, maupun data modul lain, dan setelah login
> otomatis mendarat di dashboard modulnya sendiri.

> Peserta **TOEFL juga tanpa akun** — cukup tulis nama + kelas di `/toefl` lalu
> masukkan kode akses. Role `toefl_siswa` sudah dihapus bersama fitur
> registrasi/login siswa TOEFL.

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
| `RoleSeeder`      | Membuat role `super_admin`, `admin`, `guru_bk`, `admin_sibel`, dan `siswa` (role `toefl_siswa` sudah dihapus — TOEFL tanpa akun) |
| `SettingsSeeder`  | Data pengaturan dasar website                       |
| `DatabaseSeeder`  | Memanggil `RoleSeeder` + `SettingsSeeder` + `SibelSeeder` + `ToeflSeeder`, lalu membuat 2 akun admin bila belum ada |
| `SibelSeeder`     | Kuesioner SIBEL bawaan (15 pertanyaan, status draft + perlu verifikasi) |
| `SibelDemoSeeder` | Data demo SIBEL (kelas, siswa, hasil) — jalankan manual dengan `php artisan db:seed --class=SibelDemoSeeder` |
| `SibelDummySeeder` | Data dummy lengkap dashboard SIBEL + **membuka sesi kuesioner Skala Likert** (kuesioner A/B/C diarsipkan) — jalankan manual dengan `php artisan db:seed --class=SibelDummySeeder` |
| `ToeflSeeder`     | Ujian demo TOEFL (3 bagian sesuai spesifikasi 35/25/55 mnt, 18 soal contoh, 1 kode akses portal) — **tanpa akun login**, idempoten, kode tampil sekali |
| `ToeflDummySeeder` | Data dummy lengkap modul TOEFL: 3 ujian (ditutup / dibuka / draf), **2 × 140 soal penuh sesuai spesifikasi**, 30 identitas peserta, 28 hasil terscore + 3 sesi berjalan, kode akses & log pemantauan — jalankan manual dengan `php artisan db:seed --class=ToeflDummySeeder` |
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
│       ├── Sibel/       # Controller modul SIBEL (gaya belajar)
│       └── Toefl/       # Controller modul TOEFL (ujian online) + Admin/
├── Policies/            # Otorisasi (mis. SibelResultPolicy)
├── Services/            # Layanan bisnis (mis. LearningStyleScoringService, Toefl*Service)
└── Models/             # Model Eloquent
config/
├── sibel.php           # Konfigurasi kategori, warna, strategi & etika SIBEL
└── toefl.php           # Konfigurasi bagian ujian, timer, audio & skor TOEFL
database/
├── data/toefl/          # Bank soal dummy TOEFL (2 paket × 140 butir)
├── migrations/         # Skema tabel
└── seeders/            # Seeder database
docs/
├── SIBEL.md            # Panduan lengkap modul SIBEL
└── TOEFL.md            # Panduan lengkap modul TOEFL ITP
resources/views/
├── admin/              # View panel admin
├── frontend/           # View halaman publik
├── auth/               # View autentikasi
├── sibel/              # View modul SIBEL (layout sendiri + halaman cetak A4)
├── toefl/              # View modul TOEFL (portal identitas, instruksi, ujian, hasil + area kelola)
└── components/         # Komponen Blade
routes/
├── web.php             # Route publik & profil
├── admin.php           # Route panel admin
├── auth.php            # Route autentikasi
├── sibel.php           # Route modul SIBEL (prefix /sibel)
└── toefl.php           # Route modul TOEFL (prefix /toefl)
```

## Lisensi

Proyek ini menggunakan lisensi **MIT**. Laravel framework juga dilisensikan di bawah [MIT license](https://opensource.org/licenses/MIT).