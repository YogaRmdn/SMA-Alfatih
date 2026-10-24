<?php

use App\Http\Controllers\Toefl\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Toefl\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Toefl\Admin\QuestionController;
use App\Http\Controllers\Toefl\Admin\ResultController as AdminResultController;
use App\Http\Controllers\Toefl\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Toefl\AudioController;
use App\Http\Controllers\Toefl\ExamController;
use App\Http\Controllers\Toefl\PortalController;
use App\Http\Controllers\Toefl\ResultController;
use App\Http\Middleware\EnsureToeflAttempt;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TOEFL ITP Level 1 — Sistem Ujian Online (TANPA registrasi & login)
|--------------------------------------------------------------------------
| Akses peserta — murni lewat session browser, alur sama seperti SIBEL:
|   portal (/toefl) -> isi nama lengkap + kelas, pilih ujian & masukkan
|   kode akses -> instruksi -> kerjakan 3 bagian -> kumpulkan -> hasil.
|   Identitas & attempt diikat ke session (`toefl_student_id` +
|   `toefl_attempt_id`); "Keluar Sesi" melepas keduanya agar satu
|   perangkat bisa dipakai bergantian oleh banyak peserta.
|   super_admin, admin, admin_toefl -> login website (/login): dashboard,
|                   kelola ujian/soal/peserta, kode akses, pantau sesi,
|                   lihat & ekspor hasil.
|
| SPESIFIKASI BAWAAN 3 BAGIAN (lihat config/toefl.php):
|   1. Listening Comprehension          — 50 soal / 35 menit
|   2. Structure and Written Expression — 40 soal / 25 menit
|   3. Reading Comprehension            — 50 soal / 55 menit
|
| KEAMANAN:
|   - Tidak ada akun/kata sandi siswa: kunci masuk adalah kode akses
|     (disimpan sebagai hash SHA-256, divalidasi + di-throttle di server).
|   - Timer dihitung dari timestamp server (deadline_at), bukan jam peserta.
|   - Batas putar audio (2x) dicatat server sebelum URL stream diterbitkan.
|   - Kunci jawaban tidak pernah masuk respons sisi peserta.
|   - Satu attempt per (peserta, ujian) — sesi ganda ditolak unique index.
|   - Session disimpan di server; `toefl_attempt_id` tidak bisa dipalsukan
|     dari browser (dicek middleware EnsureToeflAttempt).
*/

Route::prefix('toefl')
    ->name('toefl.')
    ->group(function () {

        /*
        | Portal utama — form identitas + kode, sesi berjalan, riwayat.
        | Pengelola (admin/guru BK) dialihkan ke panel kelola oleh controller.
        */
        Route::get('/', [PortalController::class, 'index'])->name('portal');

        /*
        | Masuk/keluar sesi — DI LUAR EnsureToeflAttempt karena justru
        | mengelola session attempt. `masuk` di-throttle untuk membatasi
        | percobaan menebak kode akses.
        */
        Route::post('/masuk', [PortalController::class, 'enter'])
            ->middleware('throttle:10,1')
            ->name('enter');
        Route::post('/keluar', [PortalController::class, 'leave'])->name('leave');

        /*
        | Masuk cepat sebagai "Peserta Demo" — MELEWATI kode akses. Karena itu
        | route ini menolak dirinya sendiri (404) bila APP_DEBUG=false, dan
        | tombolnya tidak dirender di view saat debug mati. Hanya untuk
        | pengembangan lokal agar alur ujian bisa dilihat tanpa kode.
        */
        Route::post('/demo', [PortalController::class, 'demo'])->name('demo');

        /*
        | Alur ujian — wajib memegang attempt yang terikat session ini.
        */
        Route::middleware(EnsureToeflAttempt::class)
            ->group(function () {
                Route::get('/instruksi', [PortalController::class, 'instructions'])->name('instructions');
                Route::post('/mulai', [PortalController::class, 'begin'])->name('begin');

                Route::get('/ujian/{section}', [ExamController::class, 'section'])
                    ->whereIn('section', ['listening', 'structure', 'reading'])
                    ->name('exam.section');
                Route::post('/jawaban', [ExamController::class, 'saveAnswer'])
                    ->middleware('throttle:120,1')
                    ->name('exam.answer');
                Route::post('/peristiwa', [ExamController::class, 'logBrowserEvent'])
                    ->middleware('throttle:120,1')
                    ->name('exam.event');
                Route::post('/kumpulkan', [ExamController::class, 'submit'])->name('exam.submit');

                Route::post('/audio/{audio}/putar', [AudioController::class, 'play'])
                    ->middleware('throttle:60,1')
                    ->name('audio.play');

                /*
                | Hasil milik peserta (session attempt).
                */
                Route::get('/hasil', [ResultController::class, 'show'])->name('result');
                Route::get('/hasil/cetak', [ResultController::class, 'print'])->name('result.print');
            });

        /*
        | Stream audio: ditandatangani (signed URL berumur pendek) + wajib
        | memiliki session attempt yang sama (session attempt hanya ada
        | setelah peserta masuk dengan kode — "Keluar Sesi" melepasnya).
        */
        Route::get('/audio/{audio}', [AudioController::class, 'stream'])
            ->name('audio.stream');

        /*
        | Sisi pengelola: admin & guru (login website).
        */
        Route::middleware(['auth', 'verified', 'role:super_admin,admin,admin_toefl'])
            ->prefix('kelola')
            ->name('admin.')
            ->group(function () {
                Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

                // Ujian.
                Route::resource('ujian', AdminExamController::class)
                    ->parameters(['ujian' => 'exam'])
                    ->except('show')
                    ->names([
                        'index' => 'exams.index',
                        'create' => 'exams.create',
                        'store' => 'exams.store',
                        'edit' => 'exams.edit',
                        'update' => 'exams.update',
                        'destroy' => 'exams.destroy',
                    ]);
                Route::get('ujian/{exam}', [AdminExamController::class, 'show'])
                    ->whereNumber('exam')
                    ->name('exams.show');
                Route::post('ujian/{exam}/status', [AdminExamController::class, 'toggleStatus'])
                    ->whereNumber('exam')
                    ->name('exams.toggle-status');
                Route::put('ujian/{exam}/bagian/{section}', [AdminExamController::class, 'updateSection'])
                    ->whereNumber('exam')
                    ->whereNumber('section')
                    ->name('exams.sections.update');

                // Kode akses ujian (level ujian — dipakai peserta saat masuk portal).
                Route::post('ujian/{exam}/kode', [AdminExamController::class, 'issueCode'])
                    ->whereNumber('exam')
                    ->middleware('throttle:30,1')
                    ->name('exams.codes.issue');
                Route::delete('ujian/{exam}/kode/{code}', [AdminExamController::class, 'revokeCode'])
                    ->whereNumber('exam')
                    ->whereNumber('code')
                    ->name('exams.codes.revoke');

                // Soal, bacaan, audio.
                Route::get('ujian/{exam}/soal', [QuestionController::class, 'index'])
                    ->whereNumber('exam')
                    ->name('questions.index');
                Route::post('ujian/{exam}/soal', [QuestionController::class, 'store'])
                    ->whereNumber('exam')
                    ->name('questions.store');
                Route::put('ujian/{exam}/soal/{question}', [QuestionController::class, 'update'])
                    ->whereNumber('exam')
                    ->whereNumber('question')
                    ->name('questions.update');
                Route::delete('ujian/{exam}/soal/{question}', [QuestionController::class, 'destroy'])
                    ->whereNumber('exam')
                    ->whereNumber('question')
                    ->name('questions.destroy');
                Route::post('ujian/{exam}/bacaan', [QuestionController::class, 'storePassage'])
                    ->whereNumber('exam')
                    ->name('passages.store');
                Route::delete('ujian/{exam}/bacaan/{passage}', [QuestionController::class, 'destroyPassage'])
                    ->whereNumber('exam')
                    ->whereNumber('passage')
                    ->name('passages.destroy');
                Route::post('ujian/{exam}/audio', [QuestionController::class, 'storeAudio'])
                    ->whereNumber('exam')
                    ->name('audio.store');
                Route::delete('ujian/{exam}/audio/{audio}', [QuestionController::class, 'destroyAudio'])
                    ->whereNumber('exam')
                    ->whereNumber('audio')
                    ->name('audio.destroy');

                // Data peserta (identitas tersimpan saat peserta masuk portal).
                Route::resource('siswa', AdminStudentController::class)
                    ->parameters(['siswa' => 'student'])
                    ->except('show')
                    ->names([
                        'index' => 'students.index',
                        'create' => 'students.create',
                        'store' => 'students.store',
                        'edit' => 'students.edit',
                        'update' => 'students.update',
                        'destroy' => 'students.destroy',
                    ]);

                // Hasil & pemantauan sesi.
                Route::get('hasil', [AdminResultController::class, 'index'])->name('results.index');
                Route::get('hasil/ekspor', [AdminResultController::class, 'export'])->name('results.export');
                Route::get('hasil/{attempt}', [AdminResultController::class, 'show'])
                    ->whereNumber('attempt')
                    ->name('results.show');
                Route::post('hasil/{attempt}/selesaikan', [AdminResultController::class, 'forceFinish'])
                    ->whereNumber('attempt')
                    ->name('results.force-finish');
                Route::post('hasil/{attempt}/buka-kembali', [AdminResultController::class, 'reopen'])
                    ->whereNumber('attempt')
                    ->name('results.reopen');
            });
    });
