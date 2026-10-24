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

SIBEL berjalan **tanpa login untuk siswa** — buka `/sibel` lalu **langsung
halaman pengisian kuesioner**: tulis nama + pilih kelas, lalu jawab
pertanyaan dalam satu halaman. Setelah kirim, identitas diikat ke **session browser**
(`sibel_student_id`, `PortalController`) sehingga halaman hasil & unduhan
terproteksi per-siswa. Route pengelola tetap dilindungi
`auth` + `verified` + `role:super_admin,admin,guru_bk,admin_sibel` (login website `/login`).

| Grup | Akses | Isi |
|------|------|-------|
| Siswa | **Tanpa login** — buka `/sibel`, tulis nama + pilih kelas, jawab kuesioner | Pengisian langsung, hasil tampil & unduh PDF miliknya |
| Pengelola | Login website, role `super_admin` / `admin` / `guru_bk` / `admin_sibel` | Dashboard, Data Siswa, Kelas, Kuesioner, Hasil, Rekap |

**Pemisahan akses:** panel SIBEL berdiri sendiri dari panel admin website.
Role `guru_bk` dan `admin_sibel` **hanya** berhak atas `/sibel` — keduanya
diblokir dari `/admin` (dan `/toefl/kelola`). Tautan pindah panel "Panel
Admin" di sidebar SIBEL hanya ditampilkan untuk `super_admin` / `admin`,
sehingga tautan yang muncul selalu benar-benar bisa diklik.

Rute utama (semua di bawah `/sibel`):

| Nama route | URI | Keterangan |
|---|---|---|
| `sibel.portal` | `/sibel` | Halaman pengisian langsung (nama + pertanyaan); pengelola diarahkan ke dashboard; belum ada sesi aktif → halaman "sesi belum dibuka" |
| `sibel.submit` | `POST /sibel/kirim` | Terima nama + jawaban, ikat sesi, nilai, tampilkan hasil (throttle 20/menit) |
| `sibel.forget` | `POST /sibel/ganti` | Lepas ikatan ("Isi untuk Nama Lain") — penting di komputer bersama |
| `sibel.dashboard` | `/sibel/kelola` | Statistik + grafik distribusi (pengelola) |
| `sibel.students.*` | `/sibel/siswa` | CRUD data siswa (identitas, bukan akun) |
| `sibel.classes.*` | `/sibel/kelas` | CRUD kelas |
| `sibel.questionnaires.*` | `/sibel/kuesioner` | CRUD kuesioner, builder pertanyaan, buka/tutup sesi, verifikasi |
| `sibel.results.*` | `/sibel/hasil` | Daftar & detail hasil, catatan guru, cetak |
| `sibel.recap.index/print` | `/sibel/rekap`, `/sibel/rekap/cetak` | Rekap kelas (filter kelas/periode/pencarian) |
| `sibel.assessment.result/print` | `/sibel/asesmen/{id}/hasil`, `/cetak` | Sisi siswa: halaman hasil & unduhan (terikat sesi) |

Akun website ber-role `siswa` (warisan sistem lama) tetap bisa login di
`/login`; setelah itu `/dashboard` mengarahkannya ke portal SIBEL dan
identitasnya otomatis terikat. `/dashboard` untuk pengelola: `guru_bk` &
`admin_sibel` → Dashboard SIBEL, selainnya → dashboard admin.

---

## 2. Alur Kerja

```
Guru BK/Admin (login /login)
  1. Buat/edit kuesioner (judul, petunjuk, versi, aturan ulang)
  2. Susun pertanyaan — tiap pertanyaan tepat 3 pilihan: A/B/C
  3. Verifikasi instrumen terhadap dokumen asli → "Tandai Terverifikasi"
  4. "Buka Sesi Pengisian"

Siswa (TANPA login)
  5. Buka /sibel → langsung halaman kuesioner: tulis nama + pilih kelas,
     jawab semua pertanyaan (indikator progres) → konfirmasi → Kirim
  6. Hasil, interpretasi & rekomendasi LANGSUNG tampil → "Unduh Hasil (PDF)"

Guru BK
  8. Review hasil, tulis catatan tindak lanjut, cetak laporan individual / rekap kelas
```

Aturan sesi & pengisian ulang:

- Sesi `draft` → belum bisa diisi; `active` → kuesioner tampil di `/sibel`
  (kuesioner aktif **terbaru** yang sudah punya pertanyaan); `closed` → halaman
  "sesi belum dibuka".
- Nama yang ditulis dicocokkan **huruf besar/kecil tidak berpengaruh** ke data
  siswa **aktif**; bila belum ada, identitas baru dibuat otomatis (sumber `self`,
  tanpa kelas — guru BK bisa lengkapi di menu Data Siswa).
- Pengisian ulang hanya bila centang **"Izinkan pengisian ulang"** dikendalikan guru BK;
  tanpa izin, pengiriman ulang atas nama yang sama **mengarahkan ke hasil lama**
  (jawaban tidak berubah, tapi tetap bisa diunduh); riwayat lama tetap tersimpan.
- **Hasil langsung tampil & bisa diunduh** begitu jawaban dikirim — tidak lagi menunggu
  publikasi guru BK (kolom `is_published` tetap ada sebagai penanda arsip, memengaruhi
  tampilan status di panel pengelola).

### Identitas siswa di komputer bersama

- Siswa **tidak memiliki akun login**. Cukup menulis nama di `/sibel`; identitas
  diikat ke session browser (`sibel_student_id`) lewat `PortalController`
  begitu jawaban dikirim.
- Karena satu perangkat bisa dipakai bergantian, tombol **"Isi untuk Nama Lain"**
  (`POST /sibel/ganti`, `sibel.forget`) tersedia di topbar dan halaman hasil —
  siswa berikutnya wajib menggunakannya bila nama yang tampil bukan namanya.
  Sesi yang sudah mengisi & tanpa izin ulang juga otomatis dilepas begitu
  guru BK menutup sesi / membuka pengisian ulang.
- Hanya siswa berstatus **aktif** yang dicocokkan namanya; siswa nonaktif tidak
  dipakai (sistem membuat identitas baru atas nama tersebut).
- **Akun siswa lama** (warisan sistem login) masih bisa login di `/login`; setelah
  itu `/dashboard` mengarahkannya ke portal dan identitasnya otomatis terikat.
  Akun tanpa role tetap diarahkan ke `/dashboard` biasa (bukan SIBEL).
- Berkas: `routes/sibel.php`, `app/Http/Controllers/Sibel/PortalController.php`,
  `app/Http/Controllers/Sibel/AssessmentController.php`,
  `app/Http/Requests/Sibel/FillQuestionnaireRequest.php`,
  `resources/views/sibel/fill.blade.php`, `resources/views/sibel/unavailable.blade.php`,
  `resources/views/sibel/layouts/student.blade.php`,
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
tidak pernah dipercaya). Tersedia **dua metode** per kuesioner (`questionnaires.scoring_method`):

### 4a. Pilihan Tunggal (A/B/C) — `scoring_method = choice` (bawaan)

1. Hitung jumlah jawaban per kategori berdasarkan `options.category_code`
   (fallback pemetaan `config('sibel.code_category')`: `A → visual`, `B → auditori`, `C → kinestetik`).
2. Skor kategori tertinggi → `dominant` (1 kategori) atau `tie` (2–3 kategori dengan skor sama).
   **Seri tidak pernah dipilih secara acak** — semua kategori teratas dicatat di `dominant_categories`.
3. Persentase = `(jawaban kategori / total jawaban valid) × 100%` (≈ 100% gabungan).
4. Interpretasi & rekomendasi dibangun dari `config/sibel.php` dengan bahasa **positif, non-menghakimi**
   ("kecenderungan, bukan satu-satunya cara belajar").
5. Percobaan mengirim jawaban tidak lengkap → `IncompleteAttemptException` → transaksi di-rollback,
   sesi tetap `in_progress`.
6. `results.attempt_id` unik — penilaian ulang pada sesi yang sama memperbarui baris yang sama (anti ganda).

### 4b. Skala Likert (4 tingkat) — `scoring_method = likert`

- Setiap pernyataan punya **satu kategori indikator** (`questions.category_code`: `visual` /
  `auditori` / `kinestetik`) dan dijawab dengan skala seragam dari `config('sibel.likert.levels')`:
  **Sangat Sesuai (4) → Sesuai (3) → Kurang Sesuai (2) → Tidak Sesuai (1)** — tanpa jawaban netral.
- **Skor kategori = jumlah nilai opsi terpilih** (`options.value`) seluruh pernyataannya;
  persentase = `(skor / skor maksimum) × 100%` dengan skor maksimum =
  `jumlah pernyataan kategori × nilai opsi tertinggi`.
- Aturan skor tertinggi, deteksi seri, interpretasi, rekomendasi, dan anti-tidak-lengkap
  **sama** seperti metode pilihan tunggal.
- Opsi skala dibuat **otomatis** oleh admin builder saat menambah pernyataan (admin hanya memilih
  teks pernyataan + kategori indikator).
- Metode dikunci setelah kuesioner punya riwayat pengisian agar hasil historis tetap konsisten;
  kuesioner lama tetap `choice` dan seluruh hasil lama tidak berubah.

> Tampilan pengisian: opsi Likert dirender sebagai **baris skala horizontal 4 kolom** pada
> `resources/views/sibel/fill.blade.php` (nama input sama → validasi & alur kirim tidak berubah).

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
- Halaman cetak: laporan individual siswa (`/sibel/asesmen/{attempt}/cetak` — tombol
  **"Unduh Hasil (PDF)"** di halaman hasil, langsung tersedia setelah jawaban dikirim),
  laporan guru BK (`/sibel/hasil/{result}/cetak`), dan rekap kelas
  (`/sibel/rekap/cetak`).
- Simpan sebagai PDF: buka halaman cetak → klik "Cetak / Simpan PDF" → pilih *Save as PDF* pada dialog.

---

## 6a. Unduhan Excel (XLSX)

- Route `GET /sibel/hasil/export` (`sibel.results.export`) — khusus pengelola
  (`role:super_admin,admin,guru_bk,admin_sibel`, terproteksi `SibelResultPolicy::viewAny`).
- Tombol **"Unduh Excel"** tersedia di **Dashboard SIBEL**, halaman **Hasil Asesmen**,
  dan halaman **Rekap Kelas** (ikut filter pencarian/kelas/periode/status publikasi yang aktif).
- Builder: `app/Exports/SibelResultsExport.php` memakai **OpenSpout** (`openspout/openspout` ^4)
  murni XLSX — tanpa PhpSpreadsheet/ext-gd, file sementara otomatis terhapus setelah dikirim.
- **3 sheet** per unduhan:
  1. **Rekap Hasil** — kop laporan (nama sekolah, judul, meta filter + waktu cetak + operator),
     tabel per siswa: tanggal, no. induk, nama, kelas, skor & persentase VAK, kategori dominan
     (seri ditandai warna ungu), status publikasi, catatan guru BK, ditutup baris **JUMLAH**.
  2. **Distribusi Kelas** — jumlah & persentase kategori dominan per kelas + baris **TOTAL SEKOLAH**.
  3. **Detail Jawaban** — satu baris per butir per siswa: pernyataan, jawaban, **nilai Likert (1–4)**
     (kolom "Nilai" diisi `-` untuk kuesioner pilihan tunggal), dan kategori indikator.
- Desain profesional bawaan: kop emerald, header putih tebal, border tipis, baris zebra,
  lebar kolom, **freeze baris header**, autofilter, gridlines mati, dan *print title*
  agar baris header ikut tercetak di tiap halaman.

---

## 7. Grafik (Chart.js)

- Chart.js v4.4.1 dimuat dari CDN `https://cdn.jsdelivr.net/npm/chart.js@4.4.1/...`
  (hanya di layout SIBEL, `defer`).
- Header CSP (`app/Http/Middleware/SecurityHeaders.php`) sudah mengizinkan `script-src https://cdn.jsdelivr.net`.
- **Fallback aman:** setiap grafik dirender lebih dulu sebagai CSS bar server-side; canvas hanya ditampilkan
  setelah Chart.js berhasil me-render. Jika CDN gagal/blokir, grafik tetap tampil (dan juga saat dicetak).

---

## 8. Keamanan & Privasi

- Validasi seluruh input di server (`FillQuestionnaireRequest`); aturan tiap jawaban dibangun
  dinamis dari daftar pertanyaan kuesioner aktif + cek `exists` **opsi milik pertanyaan tersebut**,
  ditambah validasi nama (wajib, 3–120 karakter).
- Sisi siswa tidak berbasis akun: identitas diikat ke **session browser** lewat penulisan nama
  (`POST /sibel/kirim` dibatasi throttle 20/menit); seluruh halaman hasil/cetak asesmen
  menolak (`403`) sesi yang identitasnya bukan pemilik attempt.
- Route pengelola dibatasi middleware `role:super_admin,admin,guru_bk,admin_sibel`; `SibelResultPolicy`
  kini khusus pengelola (lihat/ubah hasil).
- Skor, persentase, kategori dominan, interpretasi, dan rekomendasi **dihitung di backend** —
  frontend hanya mengirim pilihan `question_id → option_id`.
- Unique constraint DB: `answers(attempt_id, question_id)`, `options(question_id, option_code)`,
  `results(attempt_id)` — anti duplikasi di level database.
- **Implikasi penulisan nama**: siapa pun yang mengakses `/sibel` bisa menulis nama siswa mana
  pun (tanpa verifikasi) — sesuai keputusan sekolah agar pengisian bisa dilakukan bebas di
  komputer sekolah. Untuk konteks yang lebih terkendali, siapkan perangkat khusus dan
  pastikan siswa menekan "Isi untuk Nama Lain" antar-pengisian.

---

## 9. Seeder

| Seeder | Isi |
|---|---|
| `SibelSeeder` (dipanggil `DatabaseSeeder`) | 1 kuesioner bawaan (15 pertanyaan × 3 opsi), status `draft`, `needs_verification = true`, idempoten |
| `SibelLikertSeeder` (dipanggil `DatabaseSeeder`, idempoten) | Kuesioner **Skala Likert** opsional (v2.0): 15 pernyataan × 4 opsi skala (5 pernyataan per kategori indikator), status `draft`, `needs_verification = true` |
| `SibelDemoSeeder` (manual) | 3 kelas, 9 siswa, sesi & hasil contoh (termasuk skenario seri), tanpa akun login — nama siswa demo ditulis langsung di `/sibel` |
| `SibelDummySeeder` (manual, idempoten) | Data dummy dashboard: 6 kelas, 30 siswa aktif + 2 nonaktif, 4 kuesioner (sesi dibuka **Skala Likert**, kuesioner A/B/C diarsipkan `closed`, 1 draft), 62 attempts — 6 hasil Likert terbaru + 53 hasil A/B/C lampau (20 skenario seri, 45 dipublikasi) — skor dihitung mesin penilaian asli |

Jalankan demo:

```bash
php artisan db:seed --class=SibelDemoSeeder   # demo ringkas
php artisan db:seed --class=SibelDummySeeder  # data dummy dashboard lengkap
php artisan db:seed --class=SibelLikertSeeder # kuesioner Skala Likert (draft)
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
| `tests/Feature/SibelPortalTest.php` | `/sibel` langsung menampilkan kuesioner + kolom nama, nama baru membuat identitas & hasil, nama sama (huruf besar/kecil acak) memakai profil lama, nama wajib/terlalu pendek ditolak, kirim kosong tidak menyimpan apa pun, opsi milik pertanyaan lain ditolak, pengisian ulang diblokir & mengarah ke hasil lama, sudah mengisi → langsung halaman hasil, sesi ditutup → halaman kosong, ganti nama melepas ikatan, akun siswa lama otomatis terikat, pengelola diarahkan ke dashboard |
| `tests/Feature/SibelAssessmentTest.php` | Kirim lengkap → skor benar (3/1/0), anti kirim ganda, privasi hasil/cetak identitas lain (403), aturan pengisian ulang mengikuti guru BK, hasil langsung tampil + unduh tanpa publikasi, unique constraint jawaban |
| `tests/Feature/SibelAuthorizationTest.php` | Portal terbuka tanpa login vs area pengelola terproteksi, akses guru BK/admin vs siswa vs tanpa role, redirect `/dashboard` per role, halaman pengelola ter-render |
| `tests/Feature/SibelReportTest.php` | Konten cetak (identitas, skor, kesimpulan, rekomendasi, etika), hasil historis tak berubah saat instrumen diperbarui, keakuratan rekap (termasuk hasil terakhir per siswa & filter kelas/periode), filter daftar hasil, simpan catatan guru, **ekspor XLSX valid (tanda arsip ZIP, konten-type benar) & hanya untuk pengelola** |
| `tests/Feature/SibelLikertTest.php` | Skoring Likert (jumlah nilai opsi vs skor maksimum, deteksi seri), pengisian via portal tanpa login, builder otomatis 4 opsi skala + wajib kategori indikator, unduhan XLSX (3 sheet, kop, header, data siswa), akses unduhan diblokir untuk non-pengelola |

Helper bersama: `tests/Concerns/BuildsSibelData.php`.

---

## 11. Troubleshooting

| Gejala | Penyebab / solusi |
|---|---|
| Kuesioner tidak muncul di `/sibel` | Sesi masih `draft`/`closed`, atau belum ada pertanyaan — buka dari halaman kelola kuesioner |
| Nama salah menulis → data jadi ganda | Pencocokan nama huruf besar/kecil acak tapi ejaan harus sama — gabungkan lewat menu Data Siswa bila perlu |
| Siswa mengisi atas nama teman | Sifat sistem penulisan nama tanpa login — edukasi siswa & sediakan perangkat khusus bila perlu |
| Hasil tertukar antar-siswa di satu komputer | Lupa menekan "Isi untuk Nama Lain" — klik tombol itu sebelum siswa berikutnya mengisi |
| Grafik tidak muncul | CDN Chart.js diblokir — bar fallback CSS tetap tampil; izinkan `cdn.jsdelivr.net` di CSP bila perlu |
| Instrumen ditandai "Perlu Verifikasi" | Memang default; verifikasi dokumen asli lalu klik "Tandai Terverifikasi" |
| Pertanyaan tidak bisa dihapus | Sudah punya jawaban riwayat — dilindungi agar laporan historis utuh |
| Cetak tidak rapi | Gunakan halaman cetak khusus (`/cetak`) dan dialog cetak browser, pilih kertas A4 |
