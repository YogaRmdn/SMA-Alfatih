<?php

namespace App\Http\Controllers\Toefl\Admin;

use App\Http\Controllers\Controller;
use App\Models\ToeflAttempt;
use App\Models\ToeflExam;
use App\Models\ToeflSection;
use App\Services\ToeflAccessCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Kelola ujian TOEFL: CRUD + bagian (3 section baku) + publikasi +
 * penerbitan/cabutan kode akses (per ujian).
 * Jumlah soal & durasi dikonfigurasi per ujian.
 */
class ExamController extends Controller
{
    public function __construct(protected ToeflAccessCodeService $codes) {}

    public function index(): View
    {
        $exams = ToeflExam::withCount(['sections', 'attempts', 'accessCodes'])
            ->with('creator')
            ->latest('id')
            ->paginate(10);

        return view('toefl.admin.exams.index', compact('exams'));
    }

    public function create(): View
    {
        return view('toefl.admin.exams.create', [
            'sectionDefaults' => config('toefl.sections'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateExam($request);

        $exam = DB::transaction(function () use ($request, $validated) {
            $exam = ToeflExam::create([
                'created_by' => $request->user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status' => ToeflExam::STATUS_DRAFT,
                'start_at' => $validated['start_at'] ?? null,
                'end_at' => $validated['end_at'] ?? null,
                'show_result_to_student' => $request->boolean('show_result_to_student', true),
                'allow_review' => $request->boolean('allow_review', true),
                'duration_seconds' => 0,
                'total_questions' => 0,
                'exam_code' => Str::upper(Str::random(8)),
            ]);

            $this->createDefaultSections($exam, $request);

            return $exam->refresh();
        });

        return redirect()->route('toefl.admin.exams.show', $exam)
            ->with('success', 'Ujian dibuat sebagai draf. Silakan tambahkan soal lalu publikasikan.');
    }

    public function show(ToeflExam $exam): View
    {
        $exam->load(['sections.questions', 'sections.passages', 'sections.audioAssets']);

        $stats = [
            'questions' => $exam->questionCount(),
            'codes' => $exam->accessCodes()->count(),
            'attempts' => $exam->attempts()->count(),
            'finished' => $exam->attempts()->whereIn('status', [
                ToeflAttempt::STATUS_SUBMITTED,
                ToeflAttempt::STATUS_EXPIRED,
            ])->count(),
        ];

        return view('toefl.admin.exams.show', [
            'exam' => $exam,
            'stats' => $stats,
            'codes' => $exam->accessCodes()->latest()->get(),
            'sectionDefaults' => config('toefl.sections'),
        ]);
    }

    public function edit(ToeflExam $exam): View
    {
        return view('toefl.admin.exams.edit', [
            'exam' => $exam,
            'sectionDefaults' => config('toefl.sections'),
        ]);
    }

    public function update(Request $request, ToeflExam $exam): RedirectResponse
    {
        $validated = $this->validateExam($request);

        $exam->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_at' => $validated['start_at'] ?? null,
            'end_at' => $validated['end_at'] ?? null,
            'show_result_to_student' => $request->boolean('show_result_to_student'),
            'allow_review' => $request->boolean('allow_review'),
        ]);

        return redirect()->route('toefl.admin.exams.show', $exam)
            ->with('success', 'Pengaturan ujian diperbarui.');
    }

    public function destroy(ToeflExam $exam): RedirectResponse
    {
        $exam->delete();

        return redirect()->route('toefl.admin.exams.index')
            ->with('success', 'Ujian beserta soal, kode, dan hasilnya dihapus.');
    }

    /**
     * Terbitkan / tutup ujian. Publikasi mensyaratkan minimal ada soal.
     */
    public function toggleStatus(ToeflExam $exam): RedirectResponse
    {
        if ($exam->status === ToeflExam::STATUS_DRAFT && $exam->questionCount() === 0) {
            return back()->with('error', 'Tidak bisa mempublikasikan ujian tanpa soal. Tambahkan soal terlebih dahulu.');
        }

        $next = match ($exam->status) {
            ToeflExam::STATUS_DRAFT => ToeflExam::STATUS_PUBLISHED,
            ToeflExam::STATUS_PUBLISHED => ToeflExam::STATUS_CLOSED,
            default => ToeflExam::STATUS_DRAFT,
        };

        $exam->update([
            'status' => $next,
            'total_questions' => $exam->questionCount(),
            'duration_seconds' => (int) $exam->sections()->sum('duration_seconds'),
        ]);

        $label = config('toefl.exam_statuses.'.$next);

        return back()->with('success', "Status ujian diubah menjadi {$label}.");
    }

    /**
     * Perbarui durasi sebuah bagian.
     */
    public function updateSection(Request $request, ToeflExam $exam, ToeflSection $section): RedirectResponse
    {
        abort_unless($section->exam_id === $exam->id, 404);

        $validated = $request->validate([
            'duration_seconds' => ['required', 'integer', 'min:60', 'max:14400'],
            'title' => ['required', 'string', 'max:160'],
        ], [], [
            'duration_seconds' => 'durasi',
            'title' => 'judul bagian',
        ]);

        $section->update($validated);

        // Rehitung total durasi ujian (dipakai untuk deadline attempt baru).
        $exam->update(['duration_seconds' => (int) $exam->sections()->sum('duration_seconds')]);

        return back()->with('success', 'Bagian ujian diperbarui.');
    }

    /**
     * Terbitkan kode akses ujian (level ujian — dipakai peserta mana pun
     * yang tahu kodenya; identitas peserta diisi saat mereka masuk portal).
     */
    public function issueCode(Request $request, ToeflExam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'expires_at' => ['nullable', 'date'],
        ], [], [
            'expires_at' => 'berlaku sampai',
        ]);

        $issued = $this->codes->issue(
            $exam,
            isset($validated['expires_at']) ? Carbon::parse($validated['expires_at']) : null
        );

        return redirect()
            ->route('toefl.admin.exams.show', $exam)
            ->with('issued_code', $issued['code'])
            ->with('issued_exam', $exam->title)
            ->with('success', 'Kode akses berhasil dibuat. Salin sekarang — kode asli tidak ditampilkan lagi setelah halaman ditutup.');
    }

    /**
     * Cabut sebuah kode akses ujian.
     */
    public function revokeCode(ToeflExam $exam, int $codeId): RedirectResponse
    {
        $exam->accessCodes()->whereKey($codeId)->update(['revoked_at' => now()]);

        return back()->with('success', 'Kode akses dicabut.');
    }

    protected function validateExam(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
        ], [
            'end_at.after_or_equal' => 'Waktu selesai harus setelah waktu mulai.',
        ], [
            'title' => 'judul ujian',
            'start_at' => 'waktu mulai',
            'end_at' => 'waktu selesai',
        ]);
    }

    /**
     * Buat 3 bagian baku TOEFL ITP sesuai konfigurasi.
     */
    protected function createDefaultSections(ToeflExam $exam, Request $request): void
    {
        foreach (config('toefl.sections') as $type => $defaults) {
            $exam->sections()->create([
                'section_type' => $type,
                'title' => $defaults['label'],
                'duration_seconds' => (int) $request->input("section_duration.{$type}", $defaults['default_duration']),
                'section_order' => $defaults['order'],
            ]);
        }
    }
}
