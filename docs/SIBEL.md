# SIBEL — Sistem Informasi Gaya Belajar Siswa

Modul penilaian kecenderungan gaya belajar siswa untuk Guru Bimbingan dan Konseling (Guru BK).
Berjalan pada path **`/sibel`** dengan route sendiri (`routes/sibel.php`).

> **Catatan penting — instrumen BELUM diverifikasi.**
> Bunyi pertanyaan dan pemetaan `A = Visual`, `B = Auditori`, `C = Kinestetik` adalah konfigurasi awal
> yang dibuat dari rencana proyek. Seeder menandai kuesioner dengan **`needs_verification = true`**
> dan status **`draft`**. Verifikasi terhadap dokumen kuesioner asli **sebelum** penggunaan resmi,
> lalu centang "Tandai Terverifikasi" di halaman kelola kuesioner. Jangan mengubah bunyi pertanyaan
> tanpa persetujuan.

---

## Daftar Isi

1. [Akses & Role](#1-akses--role)
2. [Alur Kerja](#2-alur-kerja)
3. [Skema Database](#3-skema-database)
4. [Mesin Penilaian](#4-mesin-penilaian)
5. [Konfigurasi](#5-konfigurasi)
6. [Cetak / Simpan PDF](#6-cetak--simpan-pdf)
7. [Grafik (Chart.js)](#7-grafik-chartjs)
8. [Keamanan & Privasi](#8-keamanan--privasi)
9. [Seeder](#9-seeder)
10. [Pengujian](#10-pengujian)
11. [Troubleshooting](#11-troubleshooting)

---

## 1. Akses & Role

Semua route SIBEL diawali middleware `auth` + `verified`, lalu dibagi dua grup:

| Grup | Role | Route |
|------|------|-------|
| Pengelola | `super_admin`, `admin`, `guru_bk` | Dashboard, Data Siswa, Kelas, Kuesioner, Hasil, Rekap |
| Siswa | `siswa` | Kuesioner Saya, isi kuesioner, lihat/cetak hasil miliknya sendiri |

Rute utama (semua di bawah `/sibel`):

| Nama route | URI | Keterangan |
|---|---|---|
| `sibel.dashboard` | `/sibel` | Statistik + grafik distribusi |
| `sibel.students.*` | `/sibel/siswa` | CRUD siswa (+ akun login opsional) |
| `sibel.classes.*` | `/sibel/kelas` | CRUD kelas |
| `sibel.questionnaires.*` | `/sibel/kuesioner` | CRUD kuesioner, builder pertanyaan, buka/tutup sesi, verifikasi |
| `sibel.results.*` | `/sibel/hasil` | Daftar & detail hasil, catatan guru, publikasi, cetak |
| `sibel.recap.index/print` | `/sibel/rekap`, `/sibel/rekap/cetak` | Rekap kelas (filter kelas/periode/pencarian) |
| `sibel.assessment.*` | `/sibel/kuesioner-saya`, `/sibel/asesmen/...` | Sisi siswa: mulai, isi, kirim, lihat/cetak hasil |

Setelah login, `/dashboard` mengalirkan sesuai role: `siswa` → Kuesioner Saya, `guru_bk` → Dashboard SIBEL, selainnya → dashboard admin.

---

## 2. Alur Kerja

```
Guru BK/Admin
  1. Buat/edit kuesioner (judul, petunjuk, versi, aturan ulang)
  2. Susun pertanyaan — tiap pertanyaan tepat 3 pilihan: A/B/C
  3. Verifikasi instrumen terhadap dokumen asli → "Tandai Terverifikasi"
  4. "Buka Sesi Pengisian"

Siswa (role siswa)
  5. Login → /sibel → "Mulai Mengisi"
  6. Jawab semua pertanyaan (indikator progres) → konfirmasi → Kirim

Guru BK
  7. Review hasil, tulis catatan, centang "Publikasikan hasil ke siswa"
  8. Cetak laporan individual / rekap kelas (A4, simpan PDF via dialog cetak)

Siswa
  9. Melihat skor, interpretasi & rekomendasi SETELAH publikasi → bisa mencetak
```

Aturan sesi & pengisian ulang:

- Sesi `draft` → belum bisa diisi; `active` → siswa bisa mulai; `closed` → pengisian baru diblokir
  (sesi yang sedang berjalan tetap boleh dikirim).
- Satu siswa hanya punya **satu sesi berjalan** pada satu waktu.
- Pengisian ulang hanya bila centang **"Izinkan pengisian ulang"** dikendalikan guru BK;
  riwayat lama tetap tersimpan.
- Hasil siswa **tidak terlihat** sampai guru BK mempublikasikannya (halaman status "menunggu publikasi").

### Pendaftaran mandiri siswa (sementara)

- Tombol **"Daftar PPDB"** pada navbar website sementara diganti **"Daftar SIBEL"**
  → mengarah ke `GET/POST /sibel/daftar` — **form terbuka untuk semua** (admin yang
  sedang login pun bisa membuka form; sesi login admin tidak diganti saat menekan
  Daftar, cukup muncul pesan sukses). `POST` dibatasi `throttle:10,1`.
- Form meminta nama, email, password (+konfirmasi); **NIS dan kelas opsional** —
  kelas bisa dikosongkan lalu dipetakan guru BK lewat menu Data Siswa
  (migration `2026_10_19_000009` membuat `students.class_id` nullable).
- Pendaftaran otomatis membuat akun ber-role `siswa` (sudah `email_verified_at`) +
  profil siswa, lalu **(untuk tamu) login otomatis → langsung diarahkan ke
  `/sibel/kuesioner-saya`**. Login manual siswa pun berakhir di kuesioner
  (cabang `siswa` pada `/dashboard`).
- **Melacak siapa yang mendaftar**: profil siswa berkolom `source` (migration
  `2026_10_19_000010`) — `self` = daftar mandiri, `manual` = dibuat admin.
  Menu *Data Siswa* menampilkan lencana **"Daftar Mandiri"** dan filter **Asal**
  (Semua / Daftar mandiri / Dibuat admin).
- Footer, hero beranda, dan halaman PPDB tetap menampilkan CTA PPDB — hanya tombol
  navbar yang ditukar. Untuk mengembalikan: balikkan 2 edit di
  `resources/views/frontend/layouts/app.blade.php` (cari komentar `SEMENTARA`).
- Berkas: `routes/sibel.php` (grup publik `sibel.*`), `RegistrationController`,
  `StudentRegistrationRequest`, `sibel/auth/register.blade.php`.

---

## 3. Skema Database

Delapan tabel (migrasi `2026_10_19_000001`–`000008`):

| Tabel | Isi |
|---|---|
| `classes` | Kelas — unik `(name, academic_year)` |
| `students` | Siswa — `user_id` (akun login opsional), `class_id` |
| `questionnaires` | Kuesioner — `version`, `status`, `allow_retake`, `needs_verification` |
| `questions` | Pertanyaan — `order_number`, `is_required` |
| `options` | Pilihan jawaban — unik `(question_id, option_code)`, `category_code` |
| `attempts` | Sesi pengisian — `in_progress` / `completed` |
| `answers` | Jawaban — unik `(attempt_id, question_id)` (anti ganda) |
| `results` | Hasil — unik `attempt_id`, skor & persentase A/B/C, `dominant_categories` (JSON), `questionnaire_version`, `teacher_note`, `is_published` |

Tabel hasil bersifat **immutabel historis**: hasil tersimpan pada saat sesi dihitung (termasuk versi
instrumen). Memperbarui pertanyaan/opsi kuesioner **tidak** mengubah hasil lama — pertanyaan yang
sudah punya jawaban tidak bisa dihapus.

---

## 4. Mesin Penilaian

`app/Services/LearningStyleScoringService.php` — perhitungan **hanya di backend** (skor dari frontend
tidak pernah dipercaya).

1. Hitung jumlah jawaban per kategori berdasarkan `options.category_code`
   (fallback pemetaan `config('sibel.code_category')`: `A → visual`, `B → auditori`, `C → kinestetik`).
2. Skor kategori tertinggi → `dominant` (1 kategori) atau `tie` (2–3 kategori dengan skor sama).
   **Seri tidak pernah dipilih secara acak** — semua kategori teratas dicatat di `dominant_categories`.
3. Persentase = `(jawaban kategori / total jawaban valid) × 100%` (dibulatkan 2 desimal, ≈ 100% gabungan).
4. Interpretasi & rekomendasi dibangun dari `config/sibel.php` dengan bahasa **positif, non-menghakimi**
   ("kecenderungan, bukan satu-satunya cara belajar").
5. Percobaan mengirim jawaban tidak lengkap → `IncompleteAttemptException` → transaksi di-rollback,
   sesi tetap `in_progress`.
6. `results.attempt_id` unik — penilaian ulang pada sesi yang sama memperbarui baris yang sama (anti ganda).

---

## 5. Konfigurasi

Semua parameter kategori ada di `config/sibel.php` (aman diubah tanpa menyentuh kode):

```php
'categories' => [
    'visual'     => ['code' => 'A', 'label' => 'Visual',     'hex' => '#0ea5e9', 'description' => ..., 'strategies' => [...]],
    'auditori'   => ['code' => 'B', 'label' => 'Auditori',   'hex' => '#8b5cf6', ...],
    'kinestetik' => ['code' => 'C', 'label' => 'Kinestetik', 'hex' => '#f59e0b', ...],
],
'code_category' => ['A' => 'visual', 'B' => 'auditori', 'C' => 'kinestetik'],
'tie_label'    => 'Seri',
'ethics_note'  => 'Hasil ini merupakan ringkasan kecenderungan ... bukan diagnosis psikologis ...',
```

Mengubah label/warna/strategi cukup lewat config; **mengubah bunyi pertanyaan atau pemetaan kode**
butuh verifikasi ulang (lihat catatan di atas).

---

## 6. Cetak / Simpan PDF

- Tidak bergantung library PDF (tanpa dompdf) — memakai **dialog cetak browser** (`window.print()`).
- Layout khusus `resources/views/sibel/layouts/print.blade.php`: kertas **A4** (`@page { size: A4; margin: 12mm }`),
  kop sekolah dari tabel `settings`, blok tanda tangan Guru BK, catatan etika, toolbar tersembunyi saat dicetak.
- Halaman cetak: laporan individual siswa (`/sibel/asesmen/{attempt}/cetak` — hanya hasil terpublikasi),
  laporan guru BK (`/sibel/hasil/{result}/cetak`), dan rekap kelas (`/sibel/rekap/cetak`).
- Simpan sebagai PDF: buka halaman cetak → klik "Cetak / Simpan PDF" → pilih *Save as PDF* pada dialog.

---

## 7. Grafik (Chart.js)

- Chart.js v4.4.1 dimuat dari CDN `https://cdn.jsdelivr.net/npm/chart.js@4.4.1/...`
  (hanya di layout SIBEL, `defer`).
- Header CSP (`app/Http/Middleware/SecurityHeaders.php`) sudah mengizinkan `script-src https://cdn.jsdelivr.net`.
- **Fallback aman:** setiap grafik dirender lebih dulu sebagai CSS bar server-side; canvas hanya ditampilkan
  setelah Chart.js berhasil me-render. Jika CDN gagal/blokir, grafik tetap tampil (dan juga saat dicetak).

---

## 8. Keamanan & Privasi

- Validasi seluruh input di server (`FormRequest`); aturan tiap jawaban dibangun dinamis dari daftar
  pertanyaan + cek `exists` **opsi milik pertanyaan tersebut**.
- Route siswa dibatasi middleware `role:siswa`; route pengelola `role:super_admin,admin,guru_bk`.
- `SibelResultPolicy` (otomatis ter-discove): siswa hanya boleh melihat **hasilnya sendiri** dan
  hanya setelah dipublikasikan; pengelola boleh lihat/ubah semua.
- Sesi pengisian hanya bisa dibuka pemiliknya (`403` untuk id asing).
- Skor, persentase, kategori dominan, interpretasi, dan rekomendasi **dihitung di backend** —
  frontend hanya mengirim pilihan `question_id → option_id`.
- Unique constraint DB: `answers(attempt_id, question_id)`, `options(question_id, option_code)`,
  `results(attempt_id)` — anti duplikasi di level database.

---

## 9. Seeder

| Seeder | Isi |
|---|---|
| `SibelSeeder` (dipanggil `DatabaseSeeder`) | 1 kuesioner bawaan (15 pertanyaan × 3 opsi), status `draft`, `needs_verification = true`, idempoten |
| `SibelDemoSeeder` (manual) | 3 kelas, 9 siswa, sesi & hasil contoh (termasuk skenario seri & belum dipublikasi), 2 akun login siswa demo dengan password acak yang dicetak ke terminal |

Jalankan demo:

```bash
php artisan db:seed --class=SibelDemoSeeder
```

Membuat akun siswa: bisa lewat menu **Data Siswa → Tambah Siswa** (isi email & password — role `siswa`
dibuat otomatis), atau lewat menu Pengguna (super_admin) lalu tautkan ke data siswa.

---

## 10. Pengujian

```bash
php artisan test                 # seluruh suite (termasuk tes SIBEL)
php artisan test --filter=Sibel  # hanya tes SIBEL
php artisan test --filter=LearningStyleScoringService
vendor/bin/pint                  # format kode
```

Cakupan skenario (rencana proyek bagian 11):

| File | Skenario |
|---|---|
| `tests/Unit/LearningStyleScoringServiceTest.php` | Semua-A → Visual, semua-B → Auditori, semua-C → Kinestetik, 7/4/3 → Visual, 6/6/2 → seri, 4/4/4 → seri 3 kategori, persentase ≈ 100%, jawaban tidak lengkap ditolak, versi instrumen tersimpan, penilaian ulang tidak ganda |
| `tests/Feature/SibelAssessmentTest.php` | Mulai sesi tunggal, kirim kosong ditolak, kirim lengkap → hasil benar, anti kirim ganda, opsi milik pertanyaan lain ditolak, privasi sesi siswa lain (403), sesi ditutup, aturan pengisian ulang, gerbang publikasi + cetak, unique constraint jawaban |
| `tests/Feature/SibelAuthorizationTest.php` | Akses guru BK/admin vs siswa vs tanpa role, redirect `/dashboard` per role, halaman pengelola ter-render |
| `tests/Feature/SibelReportTest.php` | Konten cetak (identitas, skor, kesimpulan, rekomendasi, etika), hasil historis tak berubah saat instrumen diperbarui, keakuratan rekap (termasuk hasil terakhir per siswa & filter kelas/periode), filter daftar hasil, simpan catatan + publikasi |

Helper bersama: `tests/Concerns/BuildsSibelData.php`.

---

## 11. Troubleshooting

| Gejala | Penyebab / solusi |
|---|---|
| Siswa tidak bisa mulai kuesioner | Sesi masih `draft`/`closed` — buka dari halaman kelola kuesioner |
| Siswa sudah isi tapi tidak melihat hasil | Belum dipublikasikan — centang "Publikasikan" di detail hasil |
| "Data Siswa Belum Terdaftar" | Akun login belum tertaut ke data siswa — daftarkan lewat menu Data Siswa |
| Grafik tidak muncul | CDN Chart.js diblokir — bar fallback CSS tetap tampil; izinkan `cdn.jsdelivr.net` di CSP bila perlu |
| Instrumen ditandai "Perlu Verifikasi" | Memang default; verifikasi dokumen asli lalu klik "Tandai Terverifikasi" |
| Pertanyaan tidak bisa dihapus | Sudah punya jawaban riwayat — dilindungi agar laporan historis utuh |
| Cetak tidak rapi | Gunakan halaman cetak khusus (`/cetak`) dan dialog cetak browser, pilih kertas A4 |
