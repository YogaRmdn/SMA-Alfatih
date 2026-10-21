<?php

namespace App\Http\Controllers\Sibel;

use App\Http\Controllers\Controller;
use App\Models\SibelAttempt;
use Illuminate\View\View;

/**
 * Halaman hasil sisi siswa (tanpa login).
 *
 * Pengisian kuesioner kini langsung di /sibel (PortalController);
 * controller ini hanya menampilkan hasil & laporan cetak yang
 * terproteksi sesi per-siswa.
 */
class AssessmentController extends Controller
{
    /**
     * Halaman hasil siswa — langsung tampil setelah pengiriman.
     */
    public function result(SibelAttempt $attempt): View
    {
        $this->authorizeAttempt($attempt);

        abort_unless($attempt->isCompleted() && $attempt->result, 403);

        $result = $attempt->result;

        $attempt->load(['questionnaire', 'student.schoolClass']);

        return view('sibel.assessment.result', compact('attempt', 'result'));
    }

    /**
     * Cetak / simpan PDF laporan individual oleh siswa.
     */
    public function print(SibelAttempt $attempt): View
    {
        $this->authorizeAttempt($attempt);

        abort_unless($attempt->isCompleted() && $attempt->result, 403);

        $result = $attempt->result;
        $attempt->load(['questionnaire.questions.options', 'answers.option', 'student.schoolClass']);

        return view('sibel.results.print', compact('attempt', 'result'));
    }

    /**
     * Sesi hanya boleh dibuka oleh identitas siswa yang terikat di sesi
     * browser ini — uji coba privasi antar-siswa.
     */
    protected function authorizeAttempt(SibelAttempt $attempt): void
    {
        $studentId = (int) session('sibel_student_id');

        abort_unless(
            $studentId > 0 && $attempt->student && (int) $attempt->student->id === $studentId,
            403,
            'Anda tidak memiliki izin untuk mengakses hasil ini. Silakan isi kuesioner dengan nama Anda di halaman utama SIBEL.'
        );
    }
}
