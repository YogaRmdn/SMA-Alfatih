<?php

namespace App\Http\Controllers\Sibel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sibel\QuestionnaireRequest;
use App\Http\Requests\Sibel\QuestionRequest;
use App\Models\SibelAttempt;
use App\Models\SibelQuestion;
use App\Models\SibelQuestionnaire;
use App\Models\SibelStudent;
use Illuminate\Support\Facades\DB;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $questionnaires = SibelQuestionnaire::withCount('questions')
            ->withCount('attempts')
            ->withCount(['attempts as completed_attempts_count' => fn ($q) => $q->where('status', 'completed')])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('sibel.questionnaires.index', compact('questionnaires'));
    }

    public function create()
    {
        return view('sibel.questionnaires.form', [
            'questionnaire' => new SibelQuestionnaire([
                'version' => '1.0',
                'status' => SibelQuestionnaire::STATUS_DRAFT,
            ]),
        ]);
    }

    public function store(QuestionnaireRequest $request)
    {
        $data = $request->validated();
        $data['allow_retake'] = $request->boolean('allow_retake');
        $data['needs_verification'] = $request->boolean('needs_verification');

        SibelQuestionnaire::create($data);

        return redirect()->route('sibel.questionnaires.index')
            ->with('success', 'Kuesioner berhasil dibuat. Silakan tambahkan pertanyaan.');
    }

    public function show(SibelQuestionnaire $questionnaire)
    {
        $questionnaire->load('questions.options');

        // Status pengisian tiap siswa untuk kuesioner ini.
        $students = SibelStudent::where('status', SibelStudent::STATUS_ACTIVE)
            ->with('schoolClass')
            ->orderBy('full_name')
            ->get();

        $attempts = SibelAttempt::where('questionnaire_id', $questionnaire->id)
            ->orderByDesc('id')
            ->get()
            ->groupBy('student_id');

        $statusMap = [];
        foreach ($students as $student) {
            $attemptList = $attempts->get($student->id, collect());
            $attempt = $attemptList->firstWhere('status', SibelAttempt::STATUS_IN_PROGRESS)
                ?? $attemptList->firstWhere('status', SibelAttempt::STATUS_COMPLETED);

            $statusMap[$student->id] = $attempt;
        }

        $totalQuestions = $questionnaire->questions->count();
        $completedCount = $attempts
            ->flatten()
            ->where('status', SibelAttempt::STATUS_COMPLETED)
            ->count();
        $progressCount = $attempts
            ->flatten()
            ->where('status', SibelAttempt::STATUS_IN_PROGRESS)
            ->count();

        return view('sibel.questionnaires.show', compact(
            'questionnaire',
            'students',
            'statusMap',
            'totalQuestions',
            'completedCount',
            'progressCount'
        ));
    }

    public function edit(SibelQuestionnaire $questionnaire)
    {
        return view('sibel.questionnaires.form', compact('questionnaire'));
    }

    public function update(QuestionnaireRequest $request, SibelQuestionnaire $questionnaire)
    {
        $data = $request->validated();
        $data['allow_retake'] = $request->boolean('allow_retake');
        $data['needs_verification'] = $request->boolean('needs_verification');

        $questionnaire->update($data);

        return redirect()->route('sibel.questionnaires.index')
            ->with('success', 'Kuesioner berhasil diperbarui. Hasil historis tidak berubah.');
    }

    public function destroy(SibelQuestionnaire $questionnaire)
    {
        $questionnaire->delete();

        return redirect()->route('sibel.questionnaires.index')
            ->with('success', 'Kuesioner beserta seluruh riwayat pengisian dan hasilnya berhasil dihapus.');
    }

    /**
     * Buka/tutup sesi pengisian (draft -> dibuka -> ditutup -> dibuka).
     */
    public function toggleStatus(SibelQuestionnaire $questionnaire)
    {
        $questionnaire->status = match ($questionnaire->status) {
            SibelQuestionnaire::STATUS_DRAFT => SibelQuestionnaire::STATUS_ACTIVE,
            SibelQuestionnaire::STATUS_ACTIVE => SibelQuestionnaire::STATUS_CLOSED,
            default => SibelQuestionnaire::STATUS_ACTIVE,
        };

        $questionnaire->save();

        $label = $questionnaire->isActive() ? 'Sesi pengisian dibuka.' : 'Sesi pengisian ditutup.';

        return redirect()->route('sibel.questionnaires.show', $questionnaire)
            ->with('success', $label);
    }

    /**
     * Tandai instrumen sudah diverifikasi terhadap dokumen asli (atau belum).
     */
    public function toggleVerification(SibelQuestionnaire $questionnaire)
    {
        $questionnaire->needs_verification = ! $questionnaire->needs_verification;
        $questionnaire->save();

        $message = $questionnaire->needs_verification
            ? 'Instrumen ditandai PERLU DIVERIFIKASI.'
            : 'Instrumen ditandai sudah diverifikasi terhadap dokumen asli.';

        return redirect()->route('sibel.questionnaires.show', $questionnaire)
            ->with('success', $message);
    }

    public function storeQuestion(QuestionRequest $request, SibelQuestionnaire $questionnaire)
    {
        DB::transaction(function () use ($request, $questionnaire) {
            $data = $request->validated();

            $lastOrder = (int) $questionnaire->questions()->max('order_number');

            $question = $questionnaire->questions()->create([
                'question_text' => $data['question_text'],
                'order_number' => $data['order_number'] ?? ($lastOrder + 1),
                'is_required' => $request->boolean('is_required', true),
            ]);

            $this->syncOptions($question, $data['options']);
        });

        return redirect()->route('sibel.questionnaires.show', $questionnaire)
            ->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function updateQuestion(QuestionRequest $request, SibelQuestion $question)
    {
        DB::transaction(function () use ($request, $question) {
            $data = $request->validated();

            $question->update([
                'question_text' => $data['question_text'],
                'order_number' => $data['order_number'] ?? $question->order_number,
                'is_required' => $request->boolean('is_required', true),
            ]);

            $this->syncOptions($question, $data['options']);
        });

        return redirect()->route('sibel.questionnaires.show', $question->questionnaire)
            ->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroyQuestion(SibelQuestion $question)
    {
        if ($question->hasAnswers()) {
            return redirect()
                ->route('sibel.questionnaires.show', $question->questionnaire)
                ->with('error', 'Pertanyaan ini sudah memiliki jawaban pada riwayat pengisian dan tidak boleh dihapus agar laporan historis tetap utuh.');
        }

        $questionnaire = $question->questionnaire;
        $question->delete();

        return redirect()->route('sibel.questionnaires.show', $questionnaire)
            ->with('success', 'Pertanyaan berhasil dihapus.');
    }

    /**
     * Tulis/timpa tiga opsi A/B/C beserta pemetaan kategorinya dari konfigurasi.
     *
     * @param  array<string, string>  $options
     */
    protected function syncOptions(SibelQuestion $question, array $options): void
    {
        foreach (config('sibel.code_category') as $code => $categoryKey) {
            $question->options()->updateOrCreate(
                ['option_code' => $code],
                [
                    'option_text' => trim((string) ($options[$code] ?? '')),
                    'category_code' => $categoryKey,
                ]
            );
        }
    }
}
