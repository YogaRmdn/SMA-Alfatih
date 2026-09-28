# Laporan Audit — SMA IT Tahfizh Al-Fatih

Audit dilakukan terhadap codebase Laravel 12 di repo `YogaRmdn/SMA-Alfatih`.
Fokus: kebocoran informasi, keamanan, dan kebenaran data yang ditampilkan publik.

---

## Ringkasan

| Status | Temuan |
|--------|--------|
| 🔴 Tinggi | 3 |
| 🟡 Sedang  | 4 |
| 🔵 Rendah  | 4 |

Semua temuan **Tinggi** dan **Sedang** sudah diperbaiki, disertai regression
test. Temuan **Rendah** yang tersisa butuh keputusan produk atau tooling
eksternal dan belum ikut diubah.

---

## 🔴 Temuan Tinggi (sudah diperbaiki)

### 1. `db:seed` mengembalikan password admin ke nilai default yang diketahui publik

`DatabaseSeeder` memakai `User::updateOrCreate()` dengan password hardcoded
`password` untuk `superadmin@alfatih.sch.id` dan `admin@alfatih.sch.id`.

Dampaknya: setiap kali `php artisan db:seed` dijalankan di server (mis. saat
deploy atau Setup Ulang), password kedua akun admin **ditimpa kembali** menjadi
`password`. Nilai itu juga tertulis di `docs/PROJECT_OVERVIEW.md`, jadi siapa pun
yang membaca repo bisa langsung login ke panel admin. Ini vektor account
takeover, bukan sekadar kebocoran info.

**Perbaikan:**
- Password awal diambil dari env `SEED_ADMIN_PASSWORD`.
- Di `production`, env kosong → `db:seed` berhenti dengan exception, bukan
  diam-diam memakai password tebakan.
- Di `local`/`testing`, env kosong → password acak 24 karakter dicetak ke terminal.
- Akun yang sudah ada **tidak pernah** di-reset; seeder hanya melengkapi `role_id`.
- Kredensial dihapus dari `docs/PROJECT_OVERVIEW.md`.
- `SEED_ADMIN_PASSWORD` didokumentasikan di `.env.example` dan README.

**Test:** `tests/Feature/DatabaseSeederTest.php` (4 test).

---

### 2. Tombol & teks PPDB tetap ditampilkan saat pendaftaran ditutup

Flag `ppdb_open` di `Setting` sudah ada dan `PpdbController::create()` sudah
menampilkan halaman "closed". Tapi seluruh CTA di luar controller itu tidak
memperiksa flag tersebut:

- `frontend/layouts/app.blade.php` — navbar desktop, navbar mobile, "Menu Cepat" di footer, dan blok footer "PPDB Tahun Ajaran" yang teksnya hardcoded *"Pendaftaran Peserta Didik Baru telah dibuka."*
- `frontend/home.blade.php` — CTA hero, badge *"Pendaftaran PPDB Dibuka"*, dan tombol CTA
- `frontend/news/show.blade.php` — tombol *"Daftar PPDB Sekarang"*

Dampaknya: website menjanjikan sesuatu yang tidak benar. Pendaftar yang
menekan tombol lalu dihantarkan halaman "sedang ditutup", dan yang lebih buruk,
teks *"telah dibuka"* masih termuat sehingga bisa dipakai revitalisasi minat
pada tahun ajaran yang sudah lewat.

**Perbaikan:** semua titik tersebut kini dibungkus `@if ($ppdbOpen)`, dan
ketika ditutup tampil teks yang jujur (*"sedang ditutup"*) beserta ajakan
menghubungi sekolah. Variabel `$ppdbOpen` dihitung sekali di layout.

**Test:** 2 test di `tests/Feature/DatabaseSeederTest.php`.

---

### 3. File `public/hot` tertinggal di working tree

`public/hot` adalah marker dev server Vite. Selagi file ini ada, `@vite`
mengambil aset dari `http://127.0.0.1:5173` alih-alih `public/build`. Kalau dev
server tidak berjalan, seluruh CSS/JS tidak termuat — situs tampil tanpa gaya.
`SecurityHeaders` juga ikut menambah origin dev ke CSP.

File ini tidak pernah ter-track git (sudah ada di `.gitignore`), jadi bukan
bocoran lewat repo — hanya file lokal yang tersisa.

**Perbaikan:** file dihapus, sekalian `bootstrap/cache/*.tmp` yang tertinggal.

---

## 🟡 Temuan Sedang (sudah diperbaiki)

### 4. Validator Google Maps duplikat di dua controller

Logika validasi `maps_embed` (izinkan hanya HTTPS + host Google Maps + path
peta) disalin-mentok di `SettingController` dan `ContactController`. Dua salinan
ini bisa sewaktu-waktu diverge — dan `ContactController` punya perbedaan kecil
(daftar host tidak identik).

**Perbaikan:** diekstrak ke `App\Rules\GoogleMapsEmbed`, dipakai di kedua tempat.

### 5. `@tailwindcss/vite` tertinggal di `package.json`

Proyek memakai Tailwind **3** via `postcss.config.js`, tapi `package.json` masih
mendeklarasikan plugin Tailwind **4**. Dependensi yang tidak terpakai ini
membingungkan dan berpotensi merusak build kalau ada yang ikut memakainya.

**Perbaikan:** dihapus dari `package.json`.

### 6. `use Illuminate\Validation\Rule` tidak terpakai di `SettingController`

Import sisa. **Perbaikan:** dihapus (ditemukan `pint`).

### 7. Indentasi `@if` tidak rapi di navbar

Baris `<a>` di dalam blok `@if` tidak di-indent. Tidak merusak render, tapi
bikin diff berikutnya salah baca. **Perbaikan:** dirapikan.

---

## 🔵 Temuan Rendah (belum diperbaiki — perlu keputusan)

### 8. Aset gambar di `public/img/` berukuran 15–32 MB

| File | Ukuran |
|------|--------|
| `14.png` | 32,1 MB |
| `15.png` | 20,5 MB |
| `12.png` | 15,9 MB |

Tiga file ini dipakai sebagai banner hero (`DemoSeeder`) dan sebagai background
CTA di `home.blade.php`. Ukurannya jadi bobot unduhan terbesar di halaman utama.

**Belum diperbaiki karena:** ekstensi PHP di mesin ini tidak punya `gd` maupun
`imagick`, jadi rekompresi tidak bisa dilakukan dari repo ini. Perlu diperbaiki
manual dengan tool gambar (Squoosh / `pngquant` / `cwebp`), atau dikonversi ke
WebP + `srcset` supaya browser mobile tidak mengunduh puluhan MB.

### 9. CSP masih memakai `'unsafe-inline' 'unsafe-eval'` untuk `script-src`

Alpine.js memakai evaluator untuk expression, jadi CSP ini longgar. Memperketat
menyarankan nonce per-request, tapi itu perubahan menyentuh seluruh layout dan
komponen — belum layak dicampur dalam audit ini.

### 10. Dokumen PPDB disimpan di disk `local` (default), bukan disk private khusus

`config/filesystems.php` tidak mendefinisikan disk privat. Dokumen tetap aman
karena `storage/app` berada di luar `public/` dan aksesnya lewat session
terverifikasi, tapi praktik yang lebih baik adalah disk tersendiri (mis. `ppdb`)
supaya penambahan fitur berikutnya tidak salah menulis ke path yang terpublikasi.

### 11. `DemoSeeder` memuat NIK format realistis untuk data guru

Nomor ini sintetis (difabrikasi mengikuti format NIK), tapi formatnya identik
dengan NIK asli. Jangan pernah memakai data demo ini sebagai data nyata.

---

## Yang Sudah Aman (tidak perlu tindakan)

- **XSS konten**: `News`, `Announcement`, `Page`, `Program` melewati
  `sanitize_html()` (HTMLPurifier) sebelum disimpan. Komentar, meta title, dan
  field profil sekolah dirender dengan escaping Blade `{{ }}`.
- **Path traversal unduhan**: `DownloadController` menolak path absolut,
  backslash, dan `..` sebelum menyentuh disk.
- **Upload**: `upload_file()` menentukan ekstensi ulang dari sisi server
  (`guessExtension()`), bukan dari nama file kiriman — menutup serangan polyglot.
- **Dokumen PPDB**: hanya bisa diakses lewat session pendaftar yang sudah
  daftar atau sudah verifikasi (no. registrasi + tanggal lahir + kode akses),
  dengan rate limit.
- **Login**: password di-hash, `password` + `remember_token` disembunyikan dari
  serialisasi, middleware `role` memblokir non-admin.
- **Header keamanan**: `SecurityHeaders` mengirim CSP, `X-Frame-Options`,
  `nosniff`, `Referrer-Policy`, `Permissions-Policy`.
- **Registrasi publik**: dimatikan lewat `ALLOW_PUBLIC_REGISTRATION=false`.
- **Sitemap**: hanya memuat berita & kategori yang sudah dipublikasikan.

---

## Verifikasi

```
php artisan test     # 80 passed (762 assertions)
vendor/bin/pint      # bersih untuk file yang disentuh
```
