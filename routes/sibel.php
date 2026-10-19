<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Sibel\AssessmentController;
use App\Http\Controllers\Sibel\ClassController;
use App\Http\Controllers\Sibel\DashboardController;
use App\Http\Controllers\Sibel\QuestionnaireController;
use App\Http\Controllers\Sibel\RegistrationController;
use App\Http\Controllers\Sibel\ReportController;
use App\Http\Controllers\Sibel\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SIBEL — Sistem Informasi Gaya Belajar Siswa
|--------------------------------------------------------------------------
| Akses:
|   tamu                     -> pendaftaran mandiri siswa (/sibel/daftar)
|   siswa                     -> hanya halaman kuesioner & hasil miliknya
|   super_admin, admin, guru_bk -> dashboard, data, kuesioner, hasil, rekap
*/

/*
| Login & pendaftaran mandiri siswa — sementara menggantikan tombol "Daftar
| PPDB" pada navbar. Login SIBEL (/sibel/masuk) terpisah dari login website
| (/login) demi identitas yang profesional; keduanya memakai controller dan
| proteksi autentikasi yang sama. Halaman terbuka untuk siapa pun (termasuk
| admin yang sedang login, untuk ganti akun); otomatis-login ke pengisian
| kuesioner hanya untuk tamu.
*/
Route::prefix('sibel')
    ->name('sibel.')
    ->group(function () {
        Route::get('masuk', fn () => view('sibel.auth.login'))->name('login');
        Route::post('masuk', [AuthenticatedSessionController::class, 'store'])->name('login.store');

        Route::get('daftar', [RegistrationController::class, 'create'])->name('register');
        Route::post('daftar', [RegistrationController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('register.store');
    });

Route::middleware(['auth', 'verified'])
    ->prefix('sibel')
    ->name('sibel.')
    ->group(function () {

        /*
        | Sisi siswa: pengisian kuesioner dan hasil milik sendiri.
        */
        Route::middleware('role:siswa')->group(function () {
            Route::get('kuesioner-saya', [AssessmentController::class, 'index'])->name('assessment.index');
            Route::post('kuesioner-saya/{questionnaire}/mulai', [AssessmentController::class, 'start'])
                ->whereNumber('questionnaire')
                ->name('assessment.start');

            Route::get('asesmen/{attempt}', [AssessmentController::class, 'show'])
                ->whereNumber('attempt')
                ->name('assessment.show');
            Route::post('asesmen/{attempt}/kirim', [AssessmentController::class, 'submit'])
                ->whereNumber('attempt')
                ->name('assessment.submit');
            Route::get('asesmen/{attempt}/hasil', [AssessmentController::class, 'result'])
                ->whereNumber('attempt')
                ->name('assessment.result');
            Route::get('asesmen/{attempt}/cetak', [AssessmentController::class, 'print'])
                ->whereNumber('attempt')
                ->name('assessment.print');
        });

        /*
        | Sisi pengelola: admin & guru BK.
        */
        Route::middleware('role:super_admin,admin,guru_bk')->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

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
