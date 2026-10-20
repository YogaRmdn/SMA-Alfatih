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

SIBEL kini berjalan **tanpa login untuk siswa** — identitas siswa dipilih dari
daftar (kelas + nama) di portal `/sibel` lalu diikat ke **session browser**
(`sibel_student_id`, `PortalController`). Route pengelola tetap dilindungi
`auth` + `verified` + `role:super_admin,admin,guru_bk` (login website `/login`).

| Grup | Akses | Isi |
|------|------|-------|
| Siswa | **Tanpa login** — pilih nama di `/sibel` | Dashboard khusus pengisian kuesioner, lihat & unduh hasil miliknya |
| Pengelola | Login website, role `super_admin` / `admin` / `guru_bk` | Dashboard, Data Siswa, Kelas, Kuesioner, Hasil, Rekap |

Rute utama (semua di bawah `/sibel`):

| Nama route | URI | Keterangan |
|---|---|---|
| `sibel.portal` | `/sibel` | Pilih identitas siswa (kelas + nama); pengelola diarahkan ke dashboard |
| `sibel.identify` | `POST /sibel/pilih` | Ikat identitas siswa ke session (throttle 30/menit) |
| `sibel.forget` | `POST /sibel/ganti` | Lepas ikatan ("Ganti Siswa") — penting di komputer bersama |
| `sibel.dashboard` | `/sibel/kelola` | Statistik + grafik distribusi (pengelola) |
| `sibel.students.*` | `/sibel/siswa` | CRUD data siswa (identitas, bukan akun) |
| `sibel.classes.*` | `/sibel/kelas` | CRUD kelas |
| `sibel.questionnaires.*` | `/sibel/kuesioner` | CRUD kuesioner, builder pertanyaan, buka/tutup sesi, verifikasi |
| `sibel.results.*` | `/sibel/hasil` | Daftar & detail hasil, catatan guru, cetak |
| `sibel.recap.index/print` | `/sibel/rekap`, `/sibel/rekap/cetak` | Rekap kelas (filter kelas/periode/pencarian) |
| `sibel.assessment.*` | `/sibel/kuesioner-saya`, `/sibel/asesmen/...` | Sisi siswa: mulai, isi, kirim, lihat/cetak hasil |

Akun website ber-role `siswa` (warisan sistem lama) tetap bisa login di
`/login`; setelah itu `/dashboard` mengarahkannya ke portal SIBEL dan
identitasnya otomatis terikat. `/dashboard` untuk pengelola: `guru_bk` →
Dashboard SIBEL, selainnya → dashboard admin.

---

## 2. Alur Kerja

```
Guru BK/Admin (login /login)
  1. Buat/edit kuesioner (judul, petunjuk, versi, aturan ulang)
  2. Susun pertanyaan — tiap pertanyaan tepat 3 pilihan: A/B/C
  3. Verifikasi instrumen terhadap dokumen asli → "Tandai Terverifikasi"
  4. "Buka Sesi Pengisian"

Siswa (TANPA login)
  5. Buka /sibel → pilih kelas + nama → dashboard khusus kuesioner
  6. "Mulai Mengisi" → jawab semua pertanyaan (indikator progres) → konfirmasi → Kirim
  7. Hasil, interpretasi & rekomendasi LANGSUNG tampil → "Cetak / Simpan PDF"

Guru BK
  8. Review hasil, tulis catatan tindak lanjut, cetak laporan individual / rekap kelas
```

Aturan sesi & pengisian ulang:

- Sesi `draft` → belum bisa diisi; `active` → siswa bisa mulai; `closed` → pengisian baru diblokir
  (sesi yang sedang berjalan tetap boleh dikirim).
- Satu identitas siswa hanya punya **satu sesi berjalan** pada satu waktu.
- Pengisian ulang hanya bila centang **"Izinkan pengisian ulang"** dikendalikan guru BK;
  riwayat lama tetap tersimpan.
- **Hasil langsung tampil & bisa diunduh** begitu jawaban dikirim — tidak lagi menunggu
  publikasi guru BK (kolom `is_published` tetap ada sebagai penanda arsip, memengaruhi
  tampilan status di panel pengelola).

### Identitas siswa di komputer bersama

- Siswa **tidak memiliki akun login**. Cukup memilih nama di `/sibel`; identitas
  diikat ke session browser (`sibel_student_id`) lewat `PortalController`.
- Karena satu perangkat bisa dipakai bergantian, tombol **"Ganti Siswa"**
  (`POST /sibel/ganti`, `sibel.forget`) tersedia di topbar dan dashboard siswa —
  siswa berikutnya wajib menggunakannya bila nama yang tampil bukan namanya.
- Nama hanya muncul di daftar portal bila status siswa **aktif**; siswa nonaktif
  dan nama palsu ditolak saat pengikatan.
- **Akun siswa lama** (warisan sistem login) masih bisa login di `/login`; setelah
  itu `/dashboard` mengarahkannya ke portal dan identitasnya otomatis terikat.
  Akun tanpa role tetap diarahkan ke `/dashboard` biasa (bukan SIBEL).
- Berkas: `routes/sibel.php`, `app/Http/Controllers/Sibel/PortalController.php`,
  `resources/views/sibel/portal.blade.php`, `resources/views/sibel/layouts/student.blade.php`,
  `resources/views/sibel/assessment/*`, `components/sibel/layouts/auth.blade.php`,
  `bootstrap/app.php`.

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
- Halaman cetak: laporan individual siswa (`/sibel/asesmen/{attempt}/cetak` — langsung tersedia
  setelah jawaban dikirim), laporan guru BK (`/sibel/hasil/{result}/cetak`), dan rekap kelas
  (`/sibel/rekap/cetak`).
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
- Sisi siswa tidak berbasis akun: identitas diikat ke **session browser** lewat pemilihan nama
  (hanya siswa **aktif** yang bisa dipilih, `POST /sibel/pilih` dibatasi throttle 30/menit);
  seluruh halaman asesmen menolak (`403`) sesi yang identitasnya bukan pemilik attempt.
- Route pengelola dibatasi middleware `role:super_admin,admin,guru_bk`; `SibelResultPolicy`
  kini khusus pengelola (lihat/ubah hasil).
- Skor, persentase, kategori dominan, interpretasi, dan rekomendasi **dihitung di backend** —
  frontend hanya mengirim pilihan `question_id → option_id`.
- Unique constraint DB: `answers(attempt_id, question_id)`, `options(question_id, option_code)`,
  `results(attempt_id)` — anti duplikasi di level database.
- **Implikasi pemilihan nama**: siapa pun yang mengakses `/sibel` bisa memilih nama siswa mana
  pun (tanpa verifikasi) — sesuai keputusan sekolah agar pengisian bisa dilakukan bebas di
  komputer sekolah. Untuk konteks yang lebih terkendali, siapkan perangkat khusus dan
  pastikan siswa menekan "Ganti Siswa" antar-pengisian.

---

## 9. Seeder

| Seeder | Isi |
|---|---|
| `SibelSeeder` (dipanggil `DatabaseSeeder`) | 1 kuesioner bawaan (15 pertanyaan × 3 opsi), status `draft`, `needs_verification = true`, idempoten |
| `SibelDemoSeeder` (manual) | 3 kelas, 9 siswa, sesi & hasil contoh (termasuk skenario seri), tanpa akun login — siswa demo dipilih langsung dari portal |

Jalankan demo:

```bash
php artisan db:seed --class=SibelDemoSeeder
```

Menambahkan siswa: menu **Data Siswa → Tambah Siswa** (nama, kelas, NIS, status) —
tidak ada pembuatan akun login lagi; siswa langsung bisa mengisi lewat `/sibel`
selama statusnya aktif.

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
| `tests/Feature/SibelPortalTest.php` | Portal terbuka tanpa login, pemilihan nama mengikat session, nama kosong/tidak ada/nonaktif ditolak, ganti siswa melepas ikatan, filter kelas, akun siswa lama otomatis terikat, pengelola diarahkan ke dashboard |
| `tests/Feature/SibelAssessmentTest.php` | Tanpa identitas → portal, mulai sesi tunggal, kirim kosong ditolak, kirim lengkap → hasil benar, anti kirim ganda, opsi milik pertanyaan lain ditolak, privasi sesi identitas lain (403), sesi ditutup, aturan pengisian ulang, hasil langsung tampil + cetak tanpa publikasi, unique constraint jawaban |
| `tests/Feature/SibelAuthorizationTest.php` | Portal terbuka tanpa login vs area pengelola terproteksi, akses guru BK/admin vs siswa vs tanpa role, redirect `/dashboard` per role, halaman pengelola ter-render |
| `tests/Feature/SibelReportTest.php` | Konten cetak (identitas, skor, kesimpulan, rekomendasi, etika), hasil historis tak berubah saat instrumen diperbarui, keakuratan rekap (termasuk hasil terakhir per siswa & filter kelas/periode), filter daftar hasil, simpan catatan guru |

Helper bersama: `tests/Concerns/BuildsSibelData.php`.

---

## 11. Troubleshooting

| Gejala | Penyebab / solusi |
|---|---|
| Siswa tidak bisa mulai kuesioner | Sesi masih `draft`/`closed` — buka dari halaman kelola kuesioner |
| Nama siswa tidak muncul di portal | Status siswa bukan `aktif`, atau filter kelas sedang dipilih — ubah di menu Data Siswa |
| Siswa mengisi atas nama teman | Sifat sistem pemilihan nama tanpa login — edukasi siswa & sediakan perangkat khusus bila perlu |
| Hasil tertukar antar-siswa di satu komputer | Lupa menekan "Ganti Siswa" — klik tombol itu sebelum siswa berikutnya mengisi |
| Grafik tidak muncul | CDN Chart.js diblokir — bar fallback CSS tetap tampil; izinkan `cdn.jsdelivr.net` di CSP bila perlu |
| Instrumen ditandai "Perlu Verifikasi" | Memang default; verifikasi dokumen asli lalu klik "Tandai Terverifikasi" |
| Pertanyaan tidak bisa dihapus | Sudah punya jawaban riwayat — dilindungi agar laporan historis utuh |
| Cetak tidak rapi | Gunakan halaman cetak khusus (`/cetak`) dan dialog cetak browser, pilih kertas A4 |
