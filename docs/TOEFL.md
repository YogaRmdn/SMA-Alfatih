# TOEFL ITP Level 1 — Sistem Ujian Online

Modul latihan ujian TOEFL ITP Level 1 (Listening 50/35 mnt, Structure 40/25 mnt,
Reading 50/55 mnt — jumlah & durasi konfigurabel) pada path **`/toefl`** dengan
route sendiri (`routes/toefl.php`).

**Sistem berjalan TANPA registrasi & login** (alur sama seperti SIBEL): peserta
membuka `/toefl`, menulis **nama lengkap + kelas**, memilih ujian, lalu
memasukkan **kode akses**. Identitas & sesi ujian diikat ke **session browser**
— tidak ada akun, kata sandi, maupun guard khusus siswa. Admin/guru BK tetap
login lewat `/login` seperti biasa untuk panel kelola.

> **Catatan penting — skor latihan, bukan skor resmi ETS.**
> Skor scaled dihitung dengan aproksimasi linear 310–680, **bukan** tabel konversi
> resmi ETS. Laporan, halaman hasil, dan ekspor selalu diberi tanda **"latihan"**
> + catatan etika (`config('toefl.ethics_note')`). Pemantauan tab/layar penuh
> hanyalah catatan pendukung — **bukan** bukti kecurangan.

---

## Daftar Isi

1. [Akses & Role](#1-akses--role)
2. [Alur Kerja](#2-alur-kerja)
3. [Skema Database](#3-skema-database)
4. [API Sisi Peserta (JSON)](#4-api-sisi-peserta-json)
5. [Timer & Sesi](#5-timer--sesi)
6. [Batas Pemutaran Audio](#6-batas-pemutaran-audio)
7. [Mesin Penilaian](#7-mesin-penilaian)
8. [Pemantauan Browser](#8-pemantauan-browser)
9. [Panel Pengelola](#9-panel-pengelola)
10. [Konfigurasi](#10-konfigurasi)
11. [Keamanan & Privasi](#11-keamanan--privasi)
12. [Seeder](#12-seeder)
13. [Pengujian](#13-pengujian)
14. [Troubleshooting](#14-troubleshooting)

---

## 1. Akses & Role

Peserta **tidak perlu registrasi maupun login**. Alurnya:

buka **`/toefl`** (portal) → tulis **nama lengkap + kelas** → pilih ujian →
masukkan **kode akses** → baca instruksi → konfirmasi **Mulai Ujian** →
kerjakan 3 bagian → kumpulkan → lihat hasil.

Server mencari/membuat identitas peserta (`toefl_students`) dari input yang
ditulis, lalu mengikat **identitas + attempt ke session browser**
(`toefl_student_id` + `toefl_attempt_id`) lewat middleware `EnsureToeflAttempt`.
Karena session disimpan di server, `toefl_attempt_id` **tidak bisa dipalsukan**
dari browser.

| Grup | Akses | Isi |
|------|-------|-----|
| Peserta | **Tanpa registrasi & login** — cukup nama + kelas + kode akses di `/toefl` | Portal, instruksi, 3 bagian ujian, auto-save, hasil miliknya |
| Pengelola | Login website (`/login`), guard `web`, role `super_admin` / `admin` / **`admin_toefl`** | Dashboard, kelola ujian/soal/kode, data peserta, pantau sesi, hasil & ekspor |

Guard **`toefl`** dan role **`toefl_siswa`** sudah dihapus — modul tidak lagi
berbasis akun. Halaman login/register siswa dihapus (route `404`).

### Pemisahan dari modul SIBEL

SIBEL dan TOEFL berdiri sendiri, termasuk di level akun:

- **`admin_toefl`** hanya berhak atas panel TOEFL — `/sibel` dan `/admin`
  membalas `403`.
- **`guru_bk`** & **`admin_sibel`** hanya berhak atas SIBEL — `/toefl/kelola`
  membalas `403`.
- Setelah login tiap role mendarat di dashboard modulnya sendiri
  (`routes/web.php` → route `dashboard`), dan tujuan `url.intended` lintas
  modul dibuang oleh `AuthenticatedSessionController::mayReach()` supaya tidak
  ada yang dilempar ke halaman yang akan menolaknya.
- Menu sidebar memfilter `SIBEL Gaya Belajar` dan `TOEFL ITP Online` per role
  (`resources/views/admin/layouts/app.blade.php`).

Data juga terpisah penuh: tabel `toefl_*` (12 tabel) tidak berbagi dengan
tabel SIBEL (`students`, `attempts`, `questions`, …), tidak ada foreign key
silang, session key ber-awalan `toefl_`, dan `config/toefl.php` berdiri
sendiri. Skenario ini dites di
`tests/Feature/RoleModuleSeparationTest.php`.

**Kode akses bersifat level ujian** (bukan milik satu orang): admin menerbitkan
satu kode untuk sebuah ujian, lalu kode itu dipakai peserta mana pun yang
mengetik identitasnya. Kode disimpan sebagai hash SHA-256; hanya prefix 4 huruf
yang terlihat di panel admin.

Rute utama (semua di bawah `/toefl`):

| Nama route | URI | Keterangan |
|---|---|---|
| `toefl.portal` | `GET /toefl` | Portal utama: form identitas + kode, sesi berjalan, riwayat; pengelola → panel kelola |
| `toefl.enter` | `POST /toefl/masuk` | Validasi kode → cari/buat identitas → buat/resume attempt → ikat session (throttle 10/menit) |
| `toefl.leave` | `POST /toefl/keluar` | Lepas ikatan identitas + attempt (komputer bersama) |
| `toefl.demo` | `POST /toefl/demo` | Masuk cepat sebagai "Peserta Demo" **tanpa kode akses** — hanya saat `APP_DEBUG=true`, selain itu `404` |
| `toefl.instructions` | `GET /toefl/instruksi` | Halaman instruksi + etika |
| `toefl.begin` | `POST /toefl/mulai` | Konfirmasi mulai → `started_at` + `deadline_at` dihitung **server** |
| `toefl.exam.section` | `GET /toefl/ujian/{section}` | Bagian ujian: `listening` / `structure` / `reading` |
| `toefl.exam.answer` | `POST /toefl/jawaban` | Auto-save jawaban (throttle 120/menit) |
| `toefl.exam.event` | `POST /toefl/peristiwa` | Catat peristiwa browser (throttle 120/menit) |
| `toefl.exam.submit` | `POST /toefl/kumpulkan` | Kumpulkan & nilai di server |
| `toefl.audio.play` | `POST /toefl/audio/{audio}/putar` | Catat pemutaran → terbitkan URL stream (throttle 60/menit) |
| `toefl.audio.stream` | `GET /toefl/audio/{audio}` | Stream file privat — signed URL + session attempt |
| `toefl.result` / `toefl.result.print` | `GET /toefl/hasil` `/cetak` | Hasil & halaman cetak milik peserta |
| `toefl.admin.*` | `GET /toefl/kelola/...` | Panel pengelola (`auth` + `verified` + `role:super_admin,admin,guru_bk`) |

Tidak ada lagi `toefl.login`, `toefl.register`, `toefl.logout`,
`toefl.dashboard`, maupun `toefl.resume`. Route alur ujian & hasil melewati
`EnsureToeflAttempt` (session attempt). Tanpa session attempt → `401` (JSON) /
redirect ke `toefl.portal` (HTML).

---

## 2. Alur Kerja

```
Admin/Guru BK (login /login)
  1. Buat ujian (judul, jadwal, durasi, opsi hasil) → 3 bagian otomatis
     (Listening 50 soal/35 mnt, Structure 40/25 mnt, Reading 50/55 mnt)
  2. Susun soal per bagian (opsi A–D + kunci), bacaan Reading, unggah audio Listening
  3. Di halaman ujian → "Buat Kode Akses" → salin kode (PLAINTEXT tampil SEKALI)

Peserta (TANPA registrasi & login)
  4. Buka /toefl → tulis nama lengkap + kelas → pilih ujian → masukkan kode
  5. Server cari/buat identitas → buat/resume attempt → ikat session browser
  6. Instruksi → konfirmasi "Mulai Ujian" → deadline disetel server
  7. Kerjakan Listening → Structure → Reading; jawaban auto-save;
     audio Listening maksimal 2 putar per aset
  8. "Kumpulkan" (atau waktu habis → otomatis dinilai)
  9. Halaman hasil (bila diizinkan pengelola) + cetak

Admin
  10. Pantau sesi (status, sisa waktu, peristiwa tab), selesaikan/buka-kembali sesi,
      lihat rincian jawaban, unduh XLSX
```

Aturan penting:

- **Satu attempt per (peserta, ujian)** — unique index `toefl_attempt_one_per_student`.
  Koneksi terputus → buka `/toefl` lagi → tulis **nama + kelas yang sama**
  (huruf besar/kecil bebas) + **kode yang sama** → attempt lama di-*resume*
  (`session_version` naik), bukan attempt baru. Tidak ada tombol ulang.
- **Kode akses level ujian** — kode hanya divalidasi terhadap ujian yang
  dipilih (bukan milik satu orang); kode untuk ujian lain, kode yang dicabut
  atau kedaluwarsa → ditolak.
- **Identitas ditulis peserta** — nama + kelas yang belum ada akan membuat
  record `toefl_students` baru (`user_id` null); yang sudah terdaftar lewat
  panel admin akan dipakai ulang, bukan diduplikasi.
- Ujian `draft` → kode ditolak; `closed` → masuk ulang ditolak (sesi berjalan
  tetap dinilai server saat dikumpulkan).
- `show_result_to_student` = false → peserta diarahkan ke halaman
  "hasil disembunyikan" setelah pengumpulan.
- **Satu perangkat, banyak peserta** — tombol **"Keluar Sesi"** melepas
  identitas + attempt dari session sehingga peserta berikutnya bisa masuk
  dengan identitasnya sendiri.

---

## 3. Skema Database

Tiga belas tabel ber- prefix `toefl_` (migrasi `2026_10_23_100001`–`100012` +
`2026_10_24_000001`) — prefiks ini memisahkan modul dari tabel generik sistem
lama (`questions`, `attempts`, `answers`, `results`, `students`):

| Tabel | Isi |
|---|---|
| `toefl_students` | Identitas peserta — `student_number` unik (nullable), `class_name`, `status`, **`user_id`** nullable (sisa arsitektur akun; peserta portal dibuat tanpa akun). Identitas dibuat otomatis saat peserta masuk portal, atau didaftarkan lebih dulu oleh pengawas |
| `toefl_exams` | Ujian — `status` draft/published/closed, `total_questions`, `duration_seconds`, jadwal `start_at`/`end_at`, `show_result_to_student`, `allow_review`, `exam_code` unik |
| `toefl_sections` | 3 bagian per ujian — `section_type` (listening/structure/reading), `duration_seconds`, `section_order` |
| `toefl_passages` | Bacaan Reading — teks, urutan |
| `toefl_questions` | Soal — `question_type`, `options` (JSON A–D), **`answer_key`**, `points`, `question_order`; index `(section_id, question_order)`; `audio_asset_id` menunjuk soal Listening ber-audio |
| `toefl_audio_assets` | Berkas audio Listening — `storage_path` pada **disk privat**, `mime_type`, `size_bytes` |
| `toefl_access_codes` | Kode akses **level ujian** — `student_id` **nullable** (kode bukan milik satu orang), **`code_hash` (SHA-256)** + `code_prefix` 4 huruf untuk pencarian admin, `expires_at`, `revoked_at`, `used_at`; index `(exam_id, student_id)` |
| `toefl_attempts` | Sesi ujian — `status` in_progress/submitted/expired, `started_at`, **`deadline_at`** (server), `current_section_type`, `session_version`; **unik `(exam_id, student_id)`** |
| `toefl_answers` | Auto-save jawaban — **unik `(attempt_id, question_id)`** (anti ganda), `selected_answer`, `saved_at` |
| `toefl_audio_play_events` | Catatan pemutaran audio — **unik `(attempt_id, audio_asset_id, play_number)`** |
| `toefl_events` | Peristiwa browser — `event_type`, `event_timestamp`, `metadata` (JSON) |
| `toefl_results` | Hasil akhir — skor mentah per bagian, `total_correct`/`total_questions`, `scaled_score`, **unik `attempt_id`** (penilaian ulang meng-update baris yang sama) |

Relasi kunci: hasil bersifat **immutable historis** — mengubah soal setelah
ujian berjalan tidak mengubah hasil tersimpan; attempt selesai tidak bisa
dinilai ulang (finalize idempoten).

---

## 4. API Sisi Peserta (JSON)

Semua di bawah middleware `EnsureToeflAttempt` (session attempt yang diikat
server saat peserta masuk lewat kode). Tanpa session attempt → `401` (JSON) /
redirect ke `toefl.portal`. Endpoint JSON membalas **JSON 401/422**, bukan
redirect halaman.

| Endpoint | Payload | Sukses | Gagal |
|---|---|---|---|
| `POST /toefl/jawaban` | `question_id`, `selected_answer` (A–D / `null`) | `200 {ok, saved_at}` | `401` tanpa session · `403` soal bukan milik attempt · `409` sudah selesai · `419/422` validasi · `422 {expired:true}` lewat deadline |
| `POST /toefl/peristiwa` | `event_type` (daftar `config('toefl.event_types')`), `event_timestamp` | `200 {ok:true}` | `401` · `422` jenis tidak dikenal |
| `POST /toefl/audio/{audio}/putar` | — | `200 {ok, url, played, max, remaining}` | `401` tanpa session · `404` audio bukan ujian ini · `422` sudah selesai · **`429 {limit_exceeded:true}`** lewat batas 2 putar |
| `GET /toefl/audio/{audio}?attempt=&play=` | signed URL 10 menit | stream file (audio) | `403` tanda tangan salah/kedaluwarsa atau session attempt tidak cocok · `404` file tidak ada |

Jawaban **hanya** disimpan bila attempt masih `in_progress` dan belum lewat
`deadline_at` (dicek `ToeflAttemptService::assertActive`).

---

## 5. Timer & Sesi

- **Server-authoritative**: `deadline_at = started_at + durasi ujian + grace 30 detik`
  (`config('toefl.timer.grace_seconds')`) — dihitung saat peserta menekan
  **Mulai Ujian**, bukan saat halaman dibuka, dan bukan dari jam browser.
- Tampilan hitung mundur di layar hanya **estetika**; keputusan selalu dari
  `secondsRemaining()` di server.
- Lewat deadline → akses berikutnya (halaman/audio/jawaban) otomatis
  `finalize(STATUS_EXPIRED)`: nilai tersimpan, status jadi `Waktu Habis`.
- Pengumpulan manual setelah deadline tetap dinilai dengan status `expired`.
- **Koneksi terputus**: session hilang → buka `/toefl` lagi → tulis **nama +
  kelas yang sama** + **kode yang sama** → `resume()` (attempt & jawaban sama
  persis, `session_version` +1 untuk indikator resume). Riwayat juga tampil di
  portal bila identitas masih terikat session perangkat.
- **Sesi ganda**: pembuatan attempt kedua per (exam, student) diblokir unique
  index di level database (`UniqueConstraintViolationException`).
- **Kendali guru**: `POST /toefl/kelola/hasil/{attempt}/selesaikan` (paksa
  selesai & nilai) dan `POST .../buka-kembali` (lanjutkan sesi terbuka).

---

## 6. Batas Pemutaran Audio

Aturan: **maksimal 2 putar per aset per attempt** (`config('toefl.audio.max_plays')`),
ditegakkan `ToeflAudioLimitService`:

1. `POST putar` → transaksi + `lockForUpdate` pada attempt → hitung jumlah
   `toefl_audio_play_events` → bila sudah 2 → `429`, **tanpa URL**.
2. Baris play dicatat **sebelum** URL diterbitkan; unique index
   `(attempt, audio_asset_id, play_number)` menutup race condition permintaan
   beruntun/konkuren.
3. URL stream ditandatangani (`URL::temporarySignedRoute`, berumur **10 menit**)
   dan hanya bisa di-GET oleh session attempt yang sama; file berada pada disk
   **`local` (privat)** — di luar `public/`, tidak bisa ditebak/diakses langsung.
4. Sisa putaran ditampilkan di pemutar (`Sisa putar: x/2`).

---

## 7. Mesin Penilaian

`app/Services/ToeflScoringService.php` — **hanya di backend**; frontend tidak
pernah mengirim skor.

1. Setiap jawaban dicocokkan dengan `questions.answer_key` (huruf besar/kecil
   tidak berpengaruh) → skor mentah per bagian (`listening/structure/reading_raw_score`),
   `total_correct`, `total_questions`.
2. **Skor scaled = aproksimasi linear** pada rentang
   `config('toefl.scoring.min_scaled') = 310` … `max_scaled = 680`:
   `310 + (benar/total × 370)`, dibulatkan; tanpa soal → 0.
3. Hasil ditulis ke `toefl_results` via `updateOrCreate` — **idempoten**,
   penilaian ulang meng-update baris yang sama.
4. `finalize()` idempoten: attempt selesai → hasil lama dipakai ulang, tidak
   dinilai ulang (kecuali guru membuka kembali sesi).
5. Laporan menampilkan rincian per bagian + persentase + catatan etika
   (batas "latihan", bukan skor ETS).

---

## 8. Pemantauan Browser

`POST /toefl/peristiwa` mencatat jenis dari `config('toefl.event_types')`:
`tab_hidden`, `tab_visible`, `fullscreen_exit`, `fullscreen_enter`,
`section_enter`, `section_leave` — dengan `event_timestamp` dari client +
`metadata` (JSON). Tampil di halaman detail sesi (`/toefl/kelola/hasil/{attempt}`).

**Batasan jujur**: website biasa tidak dapat menjamin peserta tidak membuka
aplikasi lain / perangkat kedua. Peristiwa ini **catatan pendukung**, bukan
bukti kecurangan — ditegaskan di catatan etika hasil.

---

## 9. Panel Pengelola

Route `/toefl/kelola` (`toefl.admin.*`), menu sidebar **"TOEFL ITP Online"**
di `resources/views/admin/layouts/app.blade.php`:

| Menu | Halaman | Fitur |
|---|---|---|
| Dashboard TOEFL | `/toefl/kelola` | Kartu statistik (ujian, sesi, peserta, rata-rata skor) |
| Daftar Ujian | `/toefl/kelola/ujian` | CRUD ujian (3 bagian dibuat otomatis), ubah durasi per bagian, buka/tutup status, kelola soal/bacaan/audio (`/ujian/{id}/soal`), **Terbitkan/Cabut Kode Akses** (`/ujian/{id}/kode`) |
| Data Peserta | `/toefl/kelola/siswa` | CRUD data peserta — identitas juga tersimpan otomatis saat peserta menulis namanya di portal |
| Hasil & Sesi | `/toefl/kelola/hasil` | Filter daftar, detail sesi & jawaban, **selesaikan/buka-kembali sesi**, **ekspor XLSX** (`hasil/ekspor`, OpenSpout — 1 sheet rekap + kop sekolah) |

Kode akses dikelola **di halaman ujian** (bukan lagi di halaman siswa), karena
kode bersifat level ujian. Kode ditampilkan **hanya sekali** saat penerbitan;
daftar hanya menyimpan hash + prefix. Akses dilindungi
`role:super_admin,admin,guru_bk` (uji: role lain → `403`).

---

## 10. Konfigurasi

Semua parameter ada di `config/toefl.php` (aman diubah tanpa menyentuh kode):

```php
'sections' => [
    'listening' => ['label' => 'Listening Comprehension', 'default_questions' => 50, 'default_duration' => 2100, 'order' => 1],
    'structure' => ['label' => 'Structure and Written Expression', 'default_questions' => 40, 'default_duration' => 1500, 'order' => 2],
    'reading'   => ['label' => 'Reading Comprehension', 'default_questions' => 50, 'default_duration' => 3300, 'order' => 3],
],
'answer_options'  => ['A', 'B', 'C', 'D'],
'audio'           => ['max_plays' => 2, 'disk' => 'local', 'directory' => 'toefl/audio'],
'timer'           => ['grace_seconds' => 30],
'scoring'         => ['min_scaled' => 310, 'max_scaled' => 680],
'exam_statuses'   => ['draft' => 'Draf', 'published' => 'Dibuka', 'closed' => 'Ditutup'],
'event_types'     => ['tab_hidden' => ..., 'fullscreen_exit' => ...],
'instructions'    => [...],           // {audio_limit} diisi otomatis
'ethics_note'     => 'Hasil ini ... BUKAN skor resmi ETS ...',
```

- Jumlah & durasi soal **dapat berubah per ujian** lewat panel admin
  (edit bagian `PUT /toefl/kelola/ujian/{id}/bagian/{section}`).
- Audio ada di disk privat `local` → pindah ke S3/disk lain cukup ganti
  `toefl.audio.disk` (stream tetap lewat signed URL).

---

## 11. Keamanan & Privasi

- **Tanpa akun & kata sandi siswa**: guard `toefl`, role `toefl_siswa`,
  `ToeflLoginRequest`, dan `ToeflRegisterRequest` sudah dihapus; route
  `/toefl/login` & `/toefl/register` tidak ada (404). Pengelola memakai guard
  `web` seperti biasa.
- **Identitas dari input peserta**: `ToeflAccessRequest` mewajibkan
  `full_name` (maks 120) + `class_name` (maks 60) + `exam_id` + `code`,
  lalu `PortalController::resolveStudent()` mencocokkan nama & kelas
  **case-insensitive** (`LOWER(...)`) — identitas yang sama selalu memakai
  record yang sama, identitas baru dibuat otomatis.
- **Kode akses**: 8 karakter dari alfabet tanpa karakter ambigu
  (`ABCDEFGHJKLMNPQRSTUVWXYZ23456789`), format tampil `XXXX-XXXX`,
  disimpan hanya sebagai **hash SHA-256** + prefix 4 huruf (pencarian admin).
  Validasi di server: format, hash **per ujian**, status dicabut/kedaluwarsa,
  jadwal ujian, dan status identitas peserta. Route masuk diberi
  **throttle 10/menit** untuk membatasi percobaan menebak kode.
- **Session binding**: route ujian memakai middleware `EnsureToeflAttempt` —
  tanpa session attempt → `401` (JSON) / redirect `toefl.portal` (HTML);
  attempt sudah dinilai → hanya route hasil yang diizinkan. Session disimpan
  di **server**, sehingga `toefl_attempt_id` tidak bisa dibuat/diubah dari
  browser; `enter()` memanggil `session()->regenerate()` untuk mencegah
  session fixation sebelum mengikat sesi ujian.
- **Komputer bersama**: `POST /toefl/keluar` (`toefl.leave`) melepas
  identitas + attempt sekaligus — perangkat siap untuk peserta berikutnya.
  Riwayat peserta tidak lagi tampil setelah keluar.
- **Kunci jawaban tidak pernah ke sisi peserta** — payload soal lewat
  `ToeflQuestion::toPublicArray()` (id, teks, opsi, urutan — tanpa `answer_key`).
  Skor & hasil dihitung/dikembalikan server.
- **Audio privat**: file di disk `local` (bukan `public/`); akses hanya via
  signed URL 10 menit yang diterbitkan **setelah** play event tercatat, plus
  kecocokan session attempt.
- **Anti-sesi-ganda**: unique index `(exam_id, student_id)` di `toefl_attempts`.
- **Anti-race audio**: transaksi + `lockForUpdate` + unique index
  `(attempt, audio_asset_id, play_number)`.
- **Timer & nilai server**: deadline dan skor tidak pernah dipercaya dari client;
  input divalidasi FormRequest (`ToeflAccessRequest`, `ToeflAnswerRequest`,
  `ToeflEventRequest`).
- **Route pengelola**: `auth` + `verified` + `role:super_admin,admin,guru_bk`.
  Pengelola yang membuka `/toefl` dialihkan ke `/toefl/kelola` oleh
  `PortalController::index()`.
- Throttle per endpoint: masuk 10/menit, jawaban/peristiwa 120/menit, audio
 60/menit, terbit kode 30/menit.
- **Jalur demo** `POST /toefl/demo` melewati kode akses agar pengembang bisa
 langsung melihat tampilan pengerjaan. Dua pengaman: tombolnya tidak dirender
 di view, dan routenya sendiri `abort_unless(config('app.debug'), 404)` —
 jadi begitu `APP_DEBUG=false` jalur ini tidak ada sama sekali, termasuk di
 route list. Kode akses demo dibuat deterministik per ujian (`DEMO0002`,
 dst.) sehingga tombol bisa diklik berulang kali tanpa membanjiri tabel
 `toefl_access_codes`.

---

## 12. Seeder

| Seeder | Isi |
|---|---|
| `ToeflSeeder` (dipanggil `DatabaseSeeder`, idempoten) | 1 ujian demo terbit — 3 bagian **sesuai spesifikasi** (Listening 35 mnt, Structure 25 mnt, Reading 55 mnt) berisi **18 soal contoh** dengan kunci diketahui + 1 bacaan Reading, **1 kode akses portal**, **tanpa akun login** |
| `ToeflDummySeeder` (jalankan manual, idempoten) | Data dummy lengkap: 3 ujian (1 ditutup, 1 dibuka, 1 draf), **2 × 140 soal penuh sesuai spesifikasi**, 30 identitas peserta, 31 attempt (28 selesai bernilai + 3 sesi berjalan), kode akses aktif/kedaluwarsa/dicabut, log pemantauan browser |

```bash
php artisan db:seed --class=ToeflSeeder
# → Ujian demo TOEFL ITP dibuat: 3 bagian sesuai spesifikasi baku
#   (Listening 50 soal/35 mnt, Structure 40 soal/25 mnt, Reading 50 soal/55 mnt)
#   — berisi 18 soal contoh.
# → Kode akses portal: XXXX-XXXX (tampil sekali ini saja — salin sekarang)
```

### 12.1 Data dummy (untuk demo & pengembangan)

`ToeflDummySeeder` **tidak dipanggil otomatis** oleh `DatabaseSeeder`
(konsisten dengan `SibelDummySeeder` / `PpdbDemoSeeder`) supaya migrasi + seed
tetap ringan. Jalankan manual:

```bash
php artisan db:seed --class=ToeflDummySeeder
```

Isinya:

| Data | Rincian |
|---|---|
| Ujian | `Try Out TOEFL ITP Level 1 — Gelombang 1` (**ditutup**, arsip hasil), `Try Out TOEFL ITP Level 1 — Gelombang 2` (**dibuka**, bisa langsung dicoba di portal), `Latihan UTBK-TOEFL 2026 (Paket C)` (**draf**, bagian sudah dibuat, soal masih 0) |
| Bank soal | 2 paket penuh — Listening **50** soal (naskah percakapan/talk ditulis pada `question_text`), Structure **40** soal (15 kelengkapan kalimat + 25 identifikasi kesalahan), Reading **5 bacaan × 10 soal**. Berkas: `database/data/toefl/{listening,structure,reading}_{a,b}.php` |
| Peserta | 30 identitas (nama + kelas) **tanpa akun login** — sama seperti peserta yang masuk lewat portal |
| Attempt | 28 attempt selesai (termasuk 3 berstatus *waktu habis*) + 3 sesi **masih berlangsung** untuk halaman pemantauan |
| Skor | Dihitung `ToeflScoringService` asli dari jawaban benar/salah/kosong — sebaran skor tersebar di lima pita (310–680) sehingga grafik dashboard langsung terisi |
| Kode akses | Tiap ujian terbit: kode **aktif** (dicetak ke terminal), **kedaluwarsa**, dan **dicabut** |
| Peristiwa | Log masuk/keluar bagian + sebagian perpindahan tab (`tab_hidden` / `tab_visible`) |

Seeder bersifat **idempoten**: bila sebuah ujian sudah ada, isinya tidak dibuat
ulang sehingga aman dijalankan berkali-kali.

Catatan umum:

- **Tidak ada akun demo siswa** — cukup buka `/toefl`, tulis nama + kelas,
  masukkan kode akses.
- Jumlah soal `ToeflSeeder` (18) lebih sedikit dari spesifikasi baku (50/40/50)
  karena isinya **contoh latihan singkat**; bank soal `ToeflDummySeeder`
  sudah berjumlah penuh sesuai spesifikasi. Ujian baru yang dibuat lewat panel
  otomatis memakai durasi baku 2100/1500/3300 detik (lihat bagian 10).
- Kedua seeder **tanpa file audio** (Listening berupa teks naskah) — unggah
  audio asli lewat panel admin: **Daftar Ujian → Ujian → Soal → Audio**,
  lalu tautkan ke soal Listening (`audio_asset_id`).
- Seluruh butir soal adalah **contoh latihan buatan sendiri**, bukan butir
  resmi ETS.

---

## 13. Pengujian

```bash
php artisan test                    # seluruh suite (termasuk tes TOEFL)
php artisan test --filter=Toefl     # hanya tes TOEFL
vendor/bin/pint                     # format kode
```

Cakupan skenario (78 tes / 474 asersi TOEFL + 5 tes integrasi menu admin):

| File | Skenario |
|---|---|
| `tests/Feature/ToeflPortalTest.php` | Route login/register sudah tidak ada (404) & guard `toefl` hilang, portal terbuka tanpa sesi & tanpa login, **nama + kelas sama → 1 attempt saja** (case-insensitive), identitas beda → attempt terpisah, identitas buatan pengawas dipakai ulang, riwayat tampil untuk identitas terikat session, "Keluar Sesi" menghapus riwayat dari perangkat, instruksi/hasil dialihkan ke portal tanpa sesi, route masuk memakai throttle, **tombol demo hanya tampil saat `APP_DEBUG=true`**, masuk demo melompati kode akses & mengikat sesi (kode demo dibuat ulang, bukan ganda), route demo `404` saat debug mati |
| `tests/Feature/ToeflAccessCodeTest.php` | Portal menampilkan daftar ujian + form identitas, kode sah → attempt + ikat session (tanpa akun), **identitas wajib diisi**, kode salah/format bersih, kode ujian lain ditolak, ujian ditutup ditolak, kode kedaluwarsa/dicabut, **identitas nonaktif ditolak**, hash tersimpan (bukan plaintext), route ujian tanpa session → redirect portal, session attempt basi dibersihkan, keluar sesi melepas identitas + attempt, login admin → panel kelola |
| `tests/Feature/ToeflExamFlowTest.php` | Instruksi sebelum mulai (memuat nama peserta), deadline dari timestamp server, halaman bagian **tanpa kunci jawaban**, auto-save persisten pada sesi ini, soal ujian lain ditolak, tanpa session (401), opsi di luar A–D ditolak, peristiwa browser tercatat & jenis asing ditolak, pengumpulan → skor per bagian benar + scaled 310–680, hasil tampil di halaman peserta, kumpul ulang tidak menilai ulang |
| `tests/Feature/ToeflAudioLimitTest.php` | Putar 1–2 boleh (URL signed), putar ke-3 → `429` tanpa tambahan play event, 10 permintaan beruntun tidak menembus batas, audio ujian lain `404`, tanpa session `401`, setelah pengumpulan `422`, stream tanpa signature `403`, stream sah mengalirkan file privat, stream tanpa session attempt `403`, sisa putaran tampil di halaman ujian |
| `tests/Feature/ToeflTimerSessionTest.php` | Unique index menolak attempt ganda, koneksi putus → masuk ulang identitas sama → resume attempt + `session_version` naik, masuk ulang langsung ke bagian, deadline lewat → auto-finalize `expired` + hasil, jawaban lewat deadline ditolak (`expired:true`) & hasil terkunci, submit manual lewat deadline → `expired`, hasil tersembunyi saat berjalan, ujian ditutup pengawas → masuk ulang ditolak |
| `tests/Unit/ToeflScoringServiceTest.php` | Skor scaled pada batas 310/680, linier di tengah rentang, skor mentah per bagian dari kunci, jawaban kosong tidak dihitung benar, huruf kecil tetap cocok, penilaian ulang idempoten (baris sama, tanpa duplikat), persentase & rincian bagian |
| `tests/Feature/ToeflDummySeederTest.php` | `ToeflDummySeeder`: 3 ujian dengan status ditutup/dibuka/draf, tiap ujian terisi **140 soal** dengan rasio 50/40/50 dan durasi 6900 detik, ujian draf hanya kerangka, 30 identitas tanpa akun, 28 hasil (semua dari `ToeflScoringService`) berada di rentang 310–680, 3 sesi berjalan belum dinilai & deadline masih masa depan, kode akses berlaku tersedia, seeder idempoten |
| `tests/Feature/ToeflResultExportTest.php` | **Ekspor XLSX hasil** — membalas 200 dengan `content-type` & `content-disposition` benar, arsip valid (tanda `PK`), tetap valid saat belum ada hasil, dibuka tamu → dialihkan ke login, role lain → `403`. Menjaga dua bug OpenSpout v4 yang pernah membuat endpoint `500`: `new Cell()` pada kelas abstract, dan tipe kembalian `download()` |
| `tests/Feature/AdminMenuSmokeTest.php` | Sidebar menu TOEFL lengkap ("Data Peserta") + route aktif, halaman pengelola TOEFL ter-render untuk admin, **panel kode akses ada di halaman ujian**, halaman edit peserta tidak lagi menerbitkan kode, role lain → `403` |

Helper bersama: `tests/Concerns/BuildsToeflData.php`
(3 bagian + soal berkunci dengan durasi spesifikasi, identitas peserta,
kode ujian, `enterExam()` menulis nama + kelas seperti peserta nyata, mulai).

Pemisahan modul dari SIBEL diuji terpisah di
`tests/Feature/RoleModuleSeparationTest.php` (7 tes / 33 asersi):
`admin_toefl` hanya bisa membuka panel TOEFL, `guru_bk` & `admin_sibel`
diblokir dari `/toefl/kelola`, `super_admin`/`admin` membuka keduanya, tiap
role mendarat di dashboard modulnya sendiri, tujuan `url.intended` lintas
modul dibuang, dan menu sidebar hanya menampilkan modul sesuai role.

---

## 14. Troubleshooting

| Gejala | Penyebab / solusi |
|---|---|
| Tidak ada halaman login di `/toefl` | **Bukan kesalahan** — modul berjalan tanpa registrasi & login; cukup tulis nama + kelas + kode akses di portal |
| "Data peserta atas nama ini tidak aktif" | Identitas yang Anda tulis sudah di-set nonaktif di menu Data Peserta — minta pengawas mengaktifkannya |
| "Kode akses tidak ditemukan untuk ujian ini" | Kode berbeda dengan ujian yang dipilih — kode terikat satu ujian; atau salah ketik (tanda `-` boleh dihilangkan, huruf besar/kecil bebas) |
| Kode ditolak (kedaluwarsa/dicabut) | Pengawas mencabut atau melewati masa berlaku kode — minta kode baru ke pengawas |
| "Ujian ini belum dibuka atau sudah ditutup" | Status ujian `draft`/`closed` di luar jadwal `start_at`–`end_at` — hubungi pengawas |
| "Sesi ujian belum dimulai" | Halaman dibuka tanpa sesi terikat — kembali ke `/toefl` lalu isi identitas + kode |
| Kode valid tapi dibatasi (429) | Throttle masuk 10 percobaan/menit untuk mencegah tebakan — tunggu sebentar lalu coba lagi |
| Audio tidak bisa diputar | Belum ada file (seeder tanpa audio) — unggah via **Ujian → Soal → Audio** & tautkan ke soal; atau URL lewat masa 10 menit → klik Putar lagi (sisa putaran berkurang) |
| "Sisa putar 0" padahal baru sekali | Itulah batas 2 putar per aset — atur `toefl.audio.max_plays` bila kebijakan berubah |
| Hitung mundur berbeda dengan peserta lain | Normal — yang berlaku `deadline_at` server; grace 30 detik menutup keterlambatan jaringan |
| Peserta kehilangan koneksi | Buka `/toefl` lagi → tulis **nama + kelas yang sama** + **kode yang sama** → attempt & jawaban lama kembali (jangan buat identitas baru) |
| Identitas salah ketik → jadi peserta baru | Saat menulis ulang, ejaan nama + kelas harus **persis sama** (huruf besar/kecil bebas) supaya kembali ke attempt lama; record salah dapat dihapus di menu Data Peserta |
| Satu komputer dipakai ganti peserta | Klik **"Keluar Sesi"** → ikatan identitas + attempt terlepas, portal bersih untuk peserta berikutnya |
| "Ujian sudah dikumpulkan" di tengah sesi | Lewat deadline (otomatis dinilai) atau guru menyelesaikan sesi — guru bisa **Buka Kembali** di Hasil & Sesi |
| Skor tidak sama dengan TOEFL asli | Memang aproksimasi linear 310–680 untuk latihan — tabel konversi resmi ETS berbeda |
| Ekspor XLSX gagal | Pastikan `openspout/openspout` terpasang (`composer install`). Dua perangkap OpenSpout v4 yang pernah membuat endpoint ini `500` sudah diperbaiki dan kini dijaga tes: `Cell` abstract (wajib `Cell::fromValue()`, bukan `new Cell()`), dan tipe kembalian `download()` harus `BinaryFileResponse` |
