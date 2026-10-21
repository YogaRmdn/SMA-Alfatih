<?php

use App\Http\Controllers\Sibel\AssessmentController;
use App\Http\Controllers\Sibel\ClassController;
use App\Http\Controllers\Sibel\DashboardController;
use App\Http\Controllers\Sibel\PortalController;
use App\Http\Controllers\Sibel\QuestionnaireController;
use App\Http\Controllers\Sibel\ReportController;
use App\Http\Controllers\Sibel\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SIBEL — Sistem Informasi Gaya Belajar Siswa
|--------------------------------------------------------------------------
| Akses:
|   tamu (siswa)  -> buka /sibel: langsung halaman pengisian kuesioner
|                   (isi nama + jawab pertanyaan), tanpa login; setelah
|                   kirim, hasil tampil & bisa diunduh
|   super_admin, admin, guru_bk -> login website (/login): dashboard,
|                   data, kuesioner, hasil, rekap
|
| Identitas siswa diikat ke session browser (`sibel_student_id`) setelah
| jawaban dikirim (PortalController) — tidak ada akun login siswa.
*/

Route::prefix('sibel')
    ->name('sibel.')
    ->group(function () {

        /*
        | Pintu masuk siswa tanpa login: pengisian langsung (nama + jawaban),
        | ganti siswa, dan halaman hasil/unduhan.
        */
        Route::get('/', [PortalController::class, 'index'])->name('portal');
        Route::post('/kirim', [PortalController::class, 'submit'])
            ->middleware('throttle:20,1')
            ->name('submit');
        Route::post('/ganti', [PortalController::class, 'forget'])
            ->name('forget');

        Route::get('asesmen/{attempt}/hasil', [AssessmentController::class, 'result'])
            ->whereNumber('attempt')
            ->name('assessment.result');
        Route::get('asesmen/{attempt}/cetak', [AssessmentController::class, 'print'])
            ->whereNumber('attempt')
            ->name('assessment.print');

        /*
        | Sisi pengelola: admin & guru BK (login website).
        */
        Route::middleware(['auth', 'verified', 'role:super_admin,admin,guru_bk'])
            ->group(function () {
                Route::get('kelola', [DashboardController::class, 'index'])->name('dashboard');

                // Data siswa.
                Route::resource('siswa', StudentController::class)
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
                Route::get('siswa/{student}', [StudentController::class, 'show'])
                    ->whereNumber('student')
                    ->name('students.show');

                // Kelas.
                Route::resource('kelas', ClassController::class)
                    ->parameters(['kelas' => 'class'])
                    ->except('show')
                    ->names([
                        'index' => 'classes.index',
                        'create' => 'classes.create',
                        'store' => 'classes.store',
                        'edit' => 'classes.edit',
                        'update' => 'classes.update',
                        'destroy' => 'classes.destroy',
                    ]);

                // Kuesioner + pertanyaan.
                Route::post('kuesioner/{questionnaire}/status', [QuestionnaireController::class, 'toggleStatus'])
                    ->whereNumber('questionnaire')
                    ->name('questionnaires.toggle-status');
                Route::post('kuesioner/{questionnaire}/verifikasi', [QuestionnaireController::class, 'toggleVerification'])
                    ->whereNumber('questionnaire')
                    ->name('questionnaires.toggle-verification');
                Route::post('kuesioner/{questionnaire}/pertanyaan', [QuestionnaireController::class, 'storeQuestion'])
                    ->whereNumber('questionnaire')
                    ->name('questionnaires.questions.store');
                Route::put('pertanyaan/{question}', [QuestionnaireController::class, 'updateQuestion'])
                    ->whereNumber('question')
                    ->name('questionnaires.questions.update');
                Route::delete('pertanyaan/{question}', [QuestionnaireController::class, 'destroyQuestion'])
                    ->whereNumber('question')
                    ->name('questionnaires.questions.destroy');

                Route::resource('kuesioner', QuestionnaireController::class)
                    ->parameters(['kuesioner' => 'questionnaire'])
                    ->except('show')
                    ->names([
                        'index' => 'questionnaires.index',
                        'create' => 'questionnaires.create',
                        'store' => 'questionnaires.store',
                        'edit' => 'questionnaires.edit',
                        'update' => 'questionnaires.update',
                        'destroy' => 'questionnaires.destroy',
                    ]);
                Route::get('kuesioner/{questionnaire}', [QuestionnaireController::class, 'show'])
                    ->whereNumber('questionnaire')
                    ->name('questionnaires.show');

                // Hasil & laporan.
                Route::get('hasil', [ReportController::class, 'index'])->name('results.index');
                Route::get('rekap', [ReportController::class, 'recap'])->name('recap.index');
                Route::get('rekap/cetak', [ReportController::class, 'recapPrint'])->name('recap.print');
                Route::get('hasil/{result}', [ReportController::class, 'show'])
                    ->whereNumber('result')
                    ->name('results.show');
                Route::put('hasil/{result}', [ReportController::class, 'update'])
                    ->whereNumber('result')
                    ->name('results.update');
                Route::get('hasil/{result}/cetak', [ReportController::class, 'print'])
                    ->whereNumber('result')
                    ->name('results.print');
            });
    });
