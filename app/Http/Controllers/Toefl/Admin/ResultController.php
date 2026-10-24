<?php

namespace App\Http\Controllers\Toefl\Admin;

use App\Exports\ToeflResultsExport;
use App\Http\Controllers\Controller;
use App\Models\ToeflAttempt;
use App\Models\ToeflExam;
use App\Services\ToeflAttemptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Pantau sesi ujian + tinjau hasil & log peristiwa (tab/layar penuh).
 * Ekspor laporan XLSX untuk guru.
 */
class ResultController extends Controller
{
    public function __construct(protected ToeflAttemptService $attempts) {}

    public function index(Request $request): View
    {
        $examId = (int) $request->query('exam_id', 0);
        $status = (string) $request->query('status', '');

        $attempts = ToeflAttempt::with(['student', 'exam', 'result'])
            ->when($examId > 0, fn ($q) => $q->where('exam_id', $examId))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($status === ToeflAttempt::STATUS_IN_PROGRESS, fn ($q) => $q->whereNotNull('started_at'))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $exams = ToeflExam::orderBy('title')->get(['id', 'title', 'status']);

        // Finalisasi otomatis attempt yang lewat deadline tapi masih "aktif"
        // (mis. server sempat mati sebelum auto-finalize berjalan).
        foreach ($attempts as $attempt) {
            if ($attempt->isInProgress() && $attempt->started_at !== null && $attempt->hasExpired()) {
                $this->attempts->finalize($attempt, ToeflAttempt::STATUS_EXPIRED);
            }
        }

        return view('toefl.admin.results.index', compact('attempts', 'exams', 'examId', 'status'));
    }

    public function show(ToeflAttempt $attempt): View
    {
        $attempt->load([
            'student',
            'exam.sections',
            'result',
            'answers.question',
            'events',
            'audioPlayEvents.audioAsset',
        ]);

        return view('toefl.admin.results.show', compact('attempt'));
    }

    /**
     * Ambil paksa (session recovery) — finalize attempt yang macet.
     */
    public function forceFinish(ToeflAttempt $attempt): RedirectResponse
    {
        $this->attempts->finalize($attempt);

        return back()->with('success', 'Attempt diselesaikan paksa dan skor dihitung.');
    }

    /**
     * Buka kembali attempt (cabut hasil & status) — pemulihan sesi oleh guru.
     * Berguna bila siswa terputus tepat sebelum mengumpulkan.
     */
    public function reopen(ToeflAttempt $attempt): RedirectResponse
    {
        if ($attempt->exam->attempts()->where('status', ToeflAttempt::STATUS_IN_PROGRESS)->exists()) {
            return back()->with('error', 'Masih ada sesi aktif pada ujian ini.');
        }

        $attempt->update([
            'status' => ToeflAttempt::STATUS_IN_PROGRESS,
            'submitted_at' => null,
            // Beri tambahan waktu 10 menit dari sekarang (keputusan guru).
            'deadline_at' => now()->addMinutes(10),
        ]);

        $attempt->result()->delete();
        $attempt->increment('session_version');

        return back()->with('success', 'Sesi dibuka kembali (tambahan 10 menit) dan hasil lama dicabut.');
    }

    /**
     * Ekspor laporan hasil (XLSX) sesuai filter.
     */
    public function export(Request $request)
    {
        $examId = (int) $request->query('exam_id', 0);
        $status = (string) $request->query('status', '');

        $attempts = ToeflAttempt::with(['student', 'exam', 'result'])
            ->when($examId > 0, fn ($q) => $q->where('exam_id', $examId))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderBy('id')
            ->get();

        $exam = $examId > 0 ? ToeflExam::find($examId) : null;

        $meta = [
            'Ujian : '.($exam?->title ?? 'Semua ujian'),
            'Status : '.($status !== '' ? config('toefl.attempt_statuses.'.$status, $status) : 'Semua status'),
            'Dicetak : '.now()->format('d-m-Y H:i'),
            'Skor scaled bersifat aproksimasi latihan (bukan skor resmi ETS).',
        ];

        $filename = 'rekap-toefl-'.now()->format('Ymd-His').'.xlsx';

        return (new ToeflResultsExport($attempts, $meta))->download($filename);
    }
}
