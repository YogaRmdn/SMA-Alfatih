<?php

namespace App\Http\Controllers\Toefl;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToeflAnswerRequest;
use App\Http\Requests\ToeflEventRequest;
use App\Models\ToeflAttempt;
use App\Models\ToeflAudioAsset;
use App\Models\ToeflQuestion;
use App\Services\AttemptExpiredException;
use App\Services\ToeflAttemptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Sisi siswa: halaman bagian ujian, auto-save jawaban, peristiwa browser,
 * dan pengumpulan. Kunci jawaban TIDAK PERNAH disertakan pada data siswa.
 */
class ExamController extends Controller
{
    public function __construct(protected ToeflAttemptService $attempts) {}

    /**
     * Tampilkan satu bagian (listening / structure / reading).
     */
    public function section(Request $request, string $sectionType): View|RedirectResponse
    {
        $attempt = $this->attempt($request);

        if (! $attempt) {
            return redirect()->route('toefl.portal');
        }

        if ($attempt->isFinished()) {
            return redirect()->route('toefl.result');
        }

        if ($attempt->started_at === null) {
            return redirect()->route('toefl.instructions');
        }

        // Deadline server yang terlampaui -> finalisasi & lihat hasil.
        if ($attempt->hasExpired()) {
            $this->attempts->finalize($attempt, ToeflAttempt::STATUS_EXPIRED);

            return redirect()->route('toefl.result')
                ->with('info', 'Waktu ujian telah habis. Jawaban Anda telah dinilai otomatis.');
        }

        $exam = $attempt->exam;
        $section = $exam->sections()
            ->where('section_type', $sectionType)
            ->first();

        if (! $section) {
            return redirect()->route('toefl.exam.section', 'listening');
        }

        // Catat perpindahan bagian untuk log pengawasan.
        if ($attempt->current_section_type !== $sectionType) {
            $attempt->update(['current_section_type' => $sectionType]);
            $this->logEvent($attempt, 'section_enter', ['section' => $sectionType]);
        }

        $attempt->load('answers');

        $questions = $section->questions()
            ->with(['passage', 'audio'])
            ->get();

        $answers = $attempt->answers->keyBy('question_id');

        // Status pemutaran audio per aset untuk bagian ini.
        $audioStatus = $section->audioAssets->mapWithKeys(function (ToeflAudioAsset $audio) use ($attempt) {
            $played = $audio->playsFor($attempt->id);
            $max = $audio->maxPlays();

            return [$audio->id => [
                'played' => $played,
                'max' => $max,
                'remaining' => max(0, $max - $played),
            ]];
        })->all();

        $sections = $exam->sections->values();
        $currentIndex = $sections->search(fn ($s) => $s->id === $section->id);
        $prev = $currentIndex > 0 ? $sections[$currentIndex - 1] : null;
        $next = $sections->has($currentIndex + 1) ? $sections[$currentIndex + 1] : null;

        // Payload pertanyaan untuk sisi klien — tanpa answer_key.
        $questionPayload = $questions->map(fn (ToeflQuestion $q) => $q->toPublicArray())->values();

        return view('toefl.exam.section', [
            'attempt' => $attempt,
            'exam' => $exam,
            'section' => $section,
            'questions' => $questions,
            'answers' => $answers,
            'audioStatus' => $audioStatus,
            'prevSection' => $prev,
            'nextSection' => $next,
            'secondsRemaining' => $attempt->secondsRemaining(),
            'questionPayload' => $questionPayload,
        ]);
    }

    /**
     * Auto-save satu jawaban (dipanggil via fetch). Selalu server-side yang
     * memvalidasi: pemilik attempt, pertanyaan milik ujian, dan deadline.
     */
    public function saveAnswer(ToeflAnswerRequest $request): JsonResponse
    {
        $attempt = $this->attempt($request);

        if (! $attempt) {
            return response()->json(['ok' => false, 'message' => 'Sesi ujian tidak ditemukan.'], 401);
        }

        try {
            $this->attempts->assertActive($attempt);
        } catch (AttemptExpiredException $e) {
            return response()->json([
                'ok' => false,
                'expired' => true,
                'message' => $e->getMessage(),
                'redirect' => route('toefl.result'),
            ], 422);
        }

        $questionId = (int) $request->validated('question_id');
        $selected = $request->validated('selected_answer');

        // Pastikan pertanyaan benar-benar milik ujian attempt ini (bukan id acak).
        $belongs = DB::table('toefl_questions')
            ->join('toefl_sections', 'toefl_sections.id', '=', 'toefl_questions.section_id')
            ->where('toefl_questions.id', $questionId)
            ->where('toefl_sections.exam_id', $attempt->exam_id)
            ->exists();

        if (! $belongs) {
            return response()->json(['ok' => false, 'message' => 'Pertanyaan tidak termasuk ujian ini.'], 422);
        }

        DB::table('toefl_answers')->updateOrInsert(
            ['attempt_id' => $attempt->id, 'question_id' => $questionId],
            [
                'selected_answer' => $selected,
                'saved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return response()->json([
            'ok' => true,
            'saved_at' => now()->toIso8601String(),
            'seconds_remaining' => $attempt->refresh()->secondsRemaining(),
        ]);
    }

    /**
     * Catat peristiwa browser (tab tersembunyi / keluar layar penuh).
     * Hanya catatan pengawasan — BUKAN bukti kecurangan mutlak.
     */
    public function logBrowserEvent(ToeflEventRequest $request): JsonResponse
    {
        $attempt = $this->attempt($request);

        if (! $attempt) {
            return response()->json(['ok' => false], 401);
        }

        $this->logEvent($attempt, $request->validated('event_type'));

        return response()->json(['ok' => true]);
    }

    /**
     * Kumpulkan ujian (validasi deadline server sebelum menilai).
     */
    public function submit(Request $request): RedirectResponse
    {
        $attempt = $this->attempt($request);

        if (! $attempt) {
            return redirect()->route('toefl.portal');
        }

        if ($attempt->isFinished()) {
            return redirect()->route('toefl.result');
        }

        // Lewat deadline -> status expired (skor tetap dihitung dari jawaban tersimpan).
        $status = $attempt->hasExpired()
            ? ToeflAttempt::STATUS_EXPIRED
            : ToeflAttempt::STATUS_SUBMITTED;

        $this->attempts->finalize($attempt, $status);

        return redirect()->route('toefl.result')
            ->with('success', 'Ujian berhasil dikumpulkan. Hasil Anda telah dihitung.');
    }

    /**
     * Attempt dari session + refresh status kedaluwarsa.
     */
    protected function attempt(Request $request): ?ToeflAttempt
    {
        $attemptId = (int) $request->session()->get('toefl_attempt_id');

        if ($attemptId <= 0) {
            return null;
        }

        $attempt = ToeflAttempt::with('exam')->find($attemptId);

        if ($attempt && $attempt->isInProgress() && $attempt->started_at !== null && $attempt->hasExpired()) {
            $this->attempts->finalize($attempt, ToeflAttempt::STATUS_EXPIRED);
            $attempt->refresh();
        }

        return $attempt;
    }

    protected function logEvent(ToeflAttempt $attempt, string $type, array $metadata = []): void
    {
        $attempt->events()->create([
            'event_type' => $type,
            'event_timestamp' => now(),
            'metadata' => $metadata ?: null,
        ]);
    }
}
