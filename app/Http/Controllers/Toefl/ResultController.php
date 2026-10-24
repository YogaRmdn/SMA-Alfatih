<?php

namespace App\Http\Controllers\Toefl;

use App\Http\Controllers\Controller;
use App\Models\ToeflAttempt;
use App\Services\ToeflAttemptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman hasil milik siswa (hanya attempt yang terikat session ini).
 * Tampilan hasil mengikuti pengaturan `show_result_to_student` milik ujian.
 * Kunci jawaban tidak pernah ditampilkan.
 */
class ResultController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $data = $this->resolve($request);

        if ($data instanceof RedirectResponse) {
            return $data;
        }

        // Hasil disembunyikan pengawas -> tampilkan layar tunggu.
        if (! $data['exam']->show_result_to_student) {
            return view('toefl.result-hidden', ['attempt' => $data['attempt']]);
        }

        $events = $data['attempt']->events()->latest('event_timestamp')->limit(50)->get();

        return view('toefl.result', $data + ['events' => $events]);
    }

    /**
     * Cetak hasil (kertas A4, window.print()).
     */
    public function print(Request $request): View|RedirectResponse
    {
        $data = $this->resolve($request);

        if ($data instanceof RedirectResponse) {
            return $data;
        }

        if (! $data['exam']->show_result_to_student) {
            return view('toefl.result-hidden', ['attempt' => $data['attempt']]);
        }

        return view('toefl.result-print', $data);
    }

    /**
     * Muat attempt session ini + finalisasi bila lewat deadline.
     * Kembalikan data view, atau redirect bila belum siap.
     */
    protected function resolve(Request $request): array|RedirectResponse
    {
        $attemptId = (int) $request->session()->get('toefl_attempt_id');

        if ($attemptId <= 0) {
            return redirect()->route('toefl.portal');
        }

        $attempt = ToeflAttempt::with(['exam', 'student', 'result'])
            ->find($attemptId);

        if (! $attempt) {
            return redirect()->route('toefl.portal');
        }

        if ($attempt->isInProgress()) {
            if ($attempt->started_at === null) {
                return redirect()->route('toefl.instructions');
            }

            if ($attempt->hasExpired()) {
                app(ToeflAttemptService::class)
                    ->finalize($attempt, ToeflAttempt::STATUS_EXPIRED);
                $attempt->refresh();
            } else {
                return redirect()->route('toefl.exam.section', $attempt->current_section_type ?: 'listening');
            }
        }

        return [
            'attempt' => $attempt,
            'exam' => $attempt->exam,
            'result' => $attempt->result,
        ];
    }
}
