<?php

namespace App\Http\Controllers\Sibel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sibel\AssessmentSubmitRequest;
use App\Models\SibelAnswer;
use App\Models\SibelAttempt;
use App\Models\SibelQuestionnaire;
use App\Models\SibelStudent;
use App\Services\IncompleteAttemptException;
use App\Services\LearningStyleScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function __construct(protected LearningStyleScoringService $scoring) {}

    /**
     * Dashboard khusus siswa: daftar kuesioner beserta status pengisian.
     */
    public function index(): View|RedirectResponse
    {
        $student = $this->currentStudent();

        if (! $student) {
            return redirect()->route('sibel.portal');
        }

        $questionnaires = SibelQuestionnaire::withCount('questions')
            ->orderByDesc('updated_at')
            ->get();

        $attempts = $student->attempts()
            ->with('result')
            ->orderByDesc('id')
            ->get()
            ->groupBy('questionnaire_id');

        return view('sibel.assessment.index', compact('student', 'questionnaires', 'attempts'));
    }

    /**
     * Mulai sesi pengisian (atau lanjutkan sesi yang masih berjalan).
     */
    public function start(SibelQuestionnaire $questionnaire): RedirectResponse
    {
        $student = $this->currentStudent();

        abort_unless($student, 403);

        if (! $questionnaire->isActive()) {
            return redirect()->route('sibel.assessment.index')
                ->with('error', 'Sesi kuesioner ini sedang ditutup. Silakan tunggu pengumuman dari guru BK.');
        }

        if (! $questionnaire->questions()->exists()) {
            return redirect()->route('sibel.assessment.index')
                ->with('error', 'Kuesioner ini belum memiliki pertanyaan.');
        }

        // Lanjutkan sesi yang masih berjalan.
        $inProgress = $student->inProgressAttempt();
        if ($inProgress && $inProgress->questionnaire_id === $questionnaire->id) {
            return redirect()->route('sibel.assessment.show', $inProgress);
        }
        if ($inProgress) {
            return redirect()->route('sibel.assessment.show', $inProgress)
                ->with('error', 'Selesaikan kuesioner yang sedang berjalan terlebih dahulu.');
        }

        $completed = $student->attempts()
            ->where('questionnaire_id', $questionnaire->id)
            ->where('status', SibelAttempt::STATUS_COMPLETED)
            ->exists();

        if ($completed && ! $questionnaire->allow_retake) {
            return redirect()->route('sibel.assessment.index')
                ->with('error', 'Anda sudah mengisi kuesioner ini. Pengisian ulang hanya diizinkan oleh guru BK.');
        }

        $attempt = SibelAttempt::create([
            'student_id' => $student->id,
            'questionnaire_id' => $questionnaire->id,
            'status' => SibelAttempt::STATUS_IN_PROGRESS,
            'started_at' => now(),
        ]);

        return redirect()->route('sibel.assessment.show', $attempt);
    }

    /**
     * Form pengisian kuesioner (satu jawaban per pertanyaan + indikator progres).
     */
    public function show(SibelAttempt $attempt): View|RedirectResponse
    {
        $this->authorizeAttempt($attempt);

        if (! $attempt->isInProgress()) {
            return redirect()->route('sibel.assessment.index')
                ->with('error', 'Sesi pengisian ini sudah dikirim.');
        }

        $attempt->load(['questionnaire.questions.options', 'answers']);

        $answered = $attempt->answers->pluck('question_id')->flip();

        return view('sibel.assessment.take', compact('attempt', 'answered'));
    }

    /**
     * Simpan jawaban + hitung hasil (backend, transaksional, anti kirim ganda).
     */
    public function submit(AssessmentSubmitRequest $request, SibelAttempt $attempt): RedirectResponse
    {
        $this->authorizeAttempt($attempt);

        if (! $attempt->isInProgress()) {
            return redirect()->route('sibel.assessment.index')
                ->with('error', 'Sesi ini sudah pernah dikirim. Jawaban tidak diubah.');
        }

        $answers = (array) $request->input('answers', []);
        $validQuestionIds = $attempt->questionnaire->questions->pluck('id')->flip();

        try {
            DB::transaction(function () use ($attempt, $answers, $validQuestionIds) {
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
            });
        } catch (IncompleteAttemptException $e) {
            return redirect()->route('sibel.assessment.show', $attempt)
                ->withErrors(['answers' => $e->getMessage()]);
        }

        return redirect()->route('sibel.assessment.result', $attempt)
            ->with('success', 'Jawaban berhasil dikirim dan dinilai. Hasil kini bisa langsung dilihat dan diunduh.');
    }

    /**
     * Halaman hasil siswa — langsung tampil setelah pengiriman.
     */
    public function result(SibelAttempt $attempt): View|RedirectResponse
    {
        $this->authorizeAttempt($attempt);

        if (! $attempt->isCompleted() || ! $attempt->result) {
            return redirect()->route('sibel.assessment.index')
                ->with('error', 'Hasil untuk sesi ini belum tersedia.');
        }

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
     * Siswa yang sedang mengikat sesi browser ini (tanpa login).
     */
    protected function currentStudent(): ?SibelStudent
    {
        $studentId = session('sibel_student_id');

        if (! $studentId) {
            return null;
        }

        return SibelStudent::with('schoolClass')
            ->where('status', SibelStudent::STATUS_ACTIVE)
            ->find($studentId);
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
            'Anda tidak memiliki izin untuk mengakses sesi pengisian ini. Silakan pilih ulang nama Anda di halaman utama SIBEL.'
        );
    }
}
