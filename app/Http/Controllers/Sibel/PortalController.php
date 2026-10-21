<?php

namespace App\Http\Controllers\Sibel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sibel\FillQuestionnaireRequest;
use App\Models\SibelAnswer;
use App\Models\SibelAttempt;
use App\Models\SibelQuestionnaire;
use App\Models\SibelStudent;
use App\Services\IncompleteAttemptException;
use App\Services\LearningStyleScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Pintu masuk SIBEL tanpa login.
 *
 * Buka /sibel -> langsung halaman pengisian kuesioner: siswa tinggal
 * menulis namanya lalu menjawab pertanyaan (tanpa memilih dari daftar).
 * Sesi browser (`sibel_student_id`) mengikat identitas setelah kirim,
 * sehingga halaman hasil & cetak tetap terproteksi per-siswa.
 */
class PortalController extends Controller
{
    public function __construct(protected LearningStyleScoringService $scoring) {}

    /**
     * Halaman pengisian kuesioner (langsung, tanpa form identitas terpisah).
     * Pengelola (admin/guru BK) -> dashboard pengelola.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Pengelola tetap masuk ke dashboard SIBEL miliknya.
        if ($user && $user->hasRole('super_admin', 'admin', 'guru_bk')) {
            return redirect()->route('sibel.dashboard');
        }

        // Akun siswa lama (warisan sistem login) otomatis terikat ke profilnya.
        if ($user && $user->hasRole('siswa')) {
            $student = SibelStudent::where('user_id', $user->id)
                ->where('status', SibelStudent::STATUS_ACTIVE)
                ->first();

            if ($student) {
                $request->session()->put('sibel_student_id', $student->id);
            }
        }

        $questionnaire = SibelQuestionnaire::activeForFilling();

        if (! $questionnaire) {
            return view('sibel.unavailable');
        }

        $student = $this->boundStudent($request);

        // Yang sudah mengisi & tidak diizinkan ulang -> langsung lihat hasil
        // (sekalian bisa mengunduh ulang lewat tombol di halaman hasil).
        if ($student) {
            $completed = $student->attempts()
                ->where('questionnaire_id', $questionnaire->id)
                ->where('status', SibelAttempt::STATUS_COMPLETED)
                ->latest('id')
                ->first();

            if ($completed && ! $questionnaire->allow_retake) {
                return redirect()->route('sibel.assessment.result', $completed)
                    ->with('info', 'Anda sudah mengisi kuesioner ini. Berikut hasil Anda — silakan diunduh bila diperlukan.');
            }
        }

        return view('sibel.fill', compact('questionnaire', 'student'));
    }

    /**
     * Terima jawaban: ikat nama ke sesi, buat/lanjutkan sesi pengisian,
     * simpan jawaban, hitung skor, lalu tampilkan halaman hasil + unduhan.
     */
    public function submit(FillQuestionnaireRequest $request): RedirectResponse
    {
        $questionnaire = SibelQuestionnaire::activeForFilling();

        if (! $questionnaire) {
            return redirect()->route('sibel.portal')
                ->with('error', 'Sesi kuesioner belum tersedia. Silakan tunggu pengumuman dari guru BK.');
        }

        $student = $this->resolveStudent($request->input('full_name'));

        $request->session()->put('sibel_student_id', $student->id);

        // Sudah pernah mengisi & pengisian ulang tidak diizinkan -> arahkan
        // ke hasil lama (jawaban tetap tidak berubah, tapi bisa diunduh).
        $completed = $student->attempts()
            ->where('questionnaire_id', $questionnaire->id)
            ->where('status', SibelAttempt::STATUS_COMPLETED)
            ->latest('id')
            ->first();

        if ($completed && ! $questionnaire->allow_retake) {
            return redirect()->route('sibel.assessment.result', $completed)
                ->with('info', 'Anda sudah mengisi kuesioner ini. Berikut hasil Anda — silakan diunduh bila diperlukan.');
        }

        $answers = (array) $request->input('answers', []);
        $validQuestionIds = $questionnaire->questions->pluck('id')->flip();

        try {
            $attempt = DB::transaction(function () use ($student, $questionnaire, $answers, $validQuestionIds) {
                // Sesi lama yang masih berjalan (mis. dari kunjungan sebelumnya)
                // dipakai ulang; kalau sudah diizinkan ulang, buat sesi baru.
                $attempt = $student->attempts()
                    ->where('questionnaire_id', $questionnaire->id)
                    ->where('status', SibelAttempt::STATUS_IN_PROGRESS)
                    ->latest('id')
                    ->first();

                if (! $attempt) {
                    $attempt = SibelAttempt::create([
                        'student_id' => $student->id,
                        'questionnaire_id' => $questionnaire->id,
                        'status' => SibelAttempt::STATUS_IN_PROGRESS,
                        'started_at' => now(),
                    ]);
                }

                // Abaikan entri yang bukan pertanyaan kuesioner ini.
                foreach ($answers as $questionId => $optionId) {
                    if (! isset($validQuestionIds[$questionId])) {
                        continue;
                    }

                    SibelAnswer::updateOrCreate(
                        ['attempt_id' => $attempt->id, 'question_id' => (int) $questionId],
                        ['option_id' => (int) $optionId]
                    );
                }

                // Hitung skor di backend — melempar bila jawaban belum lengkap
                // dan me-rollback seluruh jawaban yang tersimpan.
                $this->scoring->scoreAndPersist($attempt);

                $attempt->update([
                    'status' => SibelAttempt::STATUS_COMPLETED,
                    'completed_at' => now(),
                ]);

                return $attempt;
            });
        } catch (IncompleteAttemptException $e) {
            return redirect()->route('sibel.portal')
                ->withInput()
                ->withErrors(['answers' => $e->getMessage()]);
        }

        return redirect()->route('sibel.assessment.result', $attempt)
            ->with('success', 'Jawaban berhasil dikirim dan dinilai. Hasil kini bisa langsung dilihat dan diunduh.');
    }

    /**
     * Lepas ikatan identitas (tombol "Isi untuk Nama Lain") — penting bila satu
     * perangkat dipakai bergantian oleh banyak siswa.
     */
    public function forget(Request $request): RedirectResponse
    {
        $request->session()->forget('sibel_student_id');

        return redirect()->route('sibel.portal')
            ->with('success', 'Identitas siswa telah dilepas. Silakan isi kuesioner untuk siswa berikutnya.');
    }

    /**
     * Siswa yang sedang mengikat sesi browser ini (tanpa login), bila masih aktif.
     */
    protected function boundStudent(Request $request): ?SibelStudent
    {
        $studentId = (int) $request->session()->get('sibel_student_id');

        if ($studentId <= 0) {
            return null;
        }

        return SibelStudent::with('schoolClass')
            ->where('status', SibelStudent::STATUS_ACTIVE)
            ->find($studentId);
    }

    /**
     * Cari identitas siswa aktif berdasarkan nama yang ditulis (huruf besar/kecil
     * tidak berpengaruh). Bila belum ada, buat identitas baru tanpa kelas —
     * guru BK bisa melengkapi datanya lewat menu Data Siswa.
     */
    protected function resolveStudent(string $name): SibelStudent
    {
        $student = SibelStudent::query()
            ->where('status', SibelStudent::STATUS_ACTIVE)
            ->whereRaw('LOWER(full_name) = ?', [mb_strtolower($name)])
            ->orderBy('id')
            ->first();

        if ($student) {
            return $student;
        }

        return SibelStudent::create([
            'full_name' => $name,
            'status' => SibelStudent::STATUS_ACTIVE,
            'source' => SibelStudent::SOURCE_SELF,
        ]);
    }
}
