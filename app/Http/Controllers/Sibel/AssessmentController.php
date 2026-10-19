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
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    public function __construct(protected LearningStyleScoringService $scoring) {}

    /**
     * Daftar kuesioner beserta status pengisian siswa yang sedang login.
     */
    public function index()
    {
        $student = $this->currentStudent();

        if (! $student) {
            return view('sibel.assessment.no-profile');
        }

        $questionnaires = SibelQuestionnaire::withCount('questions')
            ->orderByDesc('updated_at')
            ->get();

        $attempts = $student->attempts()
            ->orderByDesc('id')
            ->get()
            ->groupBy('questionnaire_id');

        return view('sibel.assessment.index', compact('student', 'questionnaires', 'attempts'));
    }

    /**
     * Mulai sesi pengisian (atau lanjutkan sesi yang masih berjalan).
     */
    public function start(SibelQuestionnaire $questionnaire)
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
    public function show(SibelAttempt $attempt)
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
    public function submit(AssessmentSubmitRequest $request, SibelAttempt $attempt)
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
            ->with('success', 'Jawaban berhasil dikirim dan dinilai. Hasil akan tampil setelah dipublikasikan guru BK.');
    }

    /**
     * Halaman hasil siswa. Tanpa publikasi guru BK, skor tidak ditampilkan.
     */
    public function result(SibelAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if (! $attempt->isCompleted() || ! $attempt->result) {
            return redirect()->route('sibel.assessment.index')
                ->with('error', 'Hasil untuk sesi ini belum tersedia.');
        }

        $result = $attempt->result;

        if (! $result->is_published) {
            return view('sibel.assessment.result-pending', compact('attempt', 'result'));
        }

        $attempt->load(['questionnaire', 'student.schoolClass']);

        return view('sibel.assessment.result', compact('attempt', 'result'));
    }

    /**
     * Cetak laporan individual oleh siswa — hanya hasil yang sudah dipublikasikan.
     */
    public function print(SibelAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        abort_unless($attempt->isCompleted() && $attempt->result?->is_published, 403);

        $result = $attempt->result;
        $attempt->load(['questionnaire.questions.options', 'answers.option', 'student.schoolClass']);

        return view('sibel.results.print', compact('attempt', 'result'));
    }

    protected function currentStudent(): ?SibelStudent
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        return SibelStudent::with('schoolClass')->where('user_id', $user->id)->first();
    }

    /**
     * Sesi hanya boleh diakses pemiliknya (siswa terkait) — uji coba privasi.
     */
    protected function authorizeAttempt(SibelAttempt $attempt): void
    {
        $user = auth()->user();

        abort_unless(
            $user && $attempt->student && $attempt->student->user_id === $user->id,
            403,
            'Anda tidak memiliki izin untuk mengakses sesi pengisian ini.'
        );
    }
}
