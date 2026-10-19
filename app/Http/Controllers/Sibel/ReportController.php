<?php

namespace App\Http\Controllers\Sibel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sibel\ResultUpdateRequest;
use App\Models\SibelAttempt;
use App\Models\SibelClass;
use App\Models\SibelResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    /**
     * Riwayat hasil (semua sesi selesai) dengan filter & pencarian.
     */
    public function index()
    {
        Gate::authorize('viewAny', SibelResult::class);

        $results = SibelResult::query()
            ->with(['attempt.student.schoolClass', 'attempt.questionnaire'])
            ->when(request('q'), fn ($q, $search) => $q->whereHas('attempt.student', fn ($s) => $s
                ->where('full_name', 'like', "%{$search}%")
                ->orWhere('student_number', 'like', "%{$search}%")))
            ->when(request('class_id'), fn ($q, $classId) => $q->whereHas('attempt.student', fn ($s) => $s->where('class_id', $classId)))
            ->when(request('published'), fn ($q, $published) => $q->where('is_published', $published === '1'))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $classes = SibelClass::orderBy('name')->get();

        return view('sibel.results.index', compact('results', 'classes'));
    }

    /**
     * Detail hasil: rekap skor, interpretasi, rekomendasi, jawaban, catatan guru.
     */
    public function show(SibelResult $result)
    {
        Gate::authorize('view', $result);

        $attempt = $result->attempt()
            ->with(['student.schoolClass', 'questionnaire', 'answers.option', 'questionnaire.questions.options'])
            ->firstOrFail();

        $answersByQuestion = $attempt->answers->keyBy('question_id');

        $otherAttempts = $attempt->student->attempts()
            ->with('questionnaire', 'result')
            ->where('id', '!=', $attempt->id)
            ->latest('id')
            ->limit(5)
            ->get();

        return view('sibel.results.show', compact('result', 'attempt', 'answersByQuestion', 'otherAttempts'));
    }

    /**
     * Simpan catatan guru BK + publikasi hasil untuk siswa.
     */
    public function update(ResultUpdateRequest $request, SibelResult $result)
    {
        Gate::authorize('update', $result);

        $data = $request->validated();
        $data['teacher_note'] = $data['teacher_note'] ?? null;
        $data['is_published'] = $request->boolean('is_published');

        $result->update($data);

        $message = $result->is_published
            ? 'Hasil dipublikasikan — siswa kini dapat melihat hasilnya.'
            : 'Hasil disembunyikan dari siswa. Catatan disimpan.';

        return redirect()->route('sibel.results.show', $result)->with('success', $message);
    }

    /**
     * Laporan individual siap cetak / simpan PDF (format kertas A4).
     */
    public function print(SibelResult $result)
    {
        Gate::authorize('view', $result);

        $attempt = $result->attempt()
            ->with(['student.schoolClass', 'questionnaire', 'answers.option', 'questionnaire.questions.options'])
            ->firstOrFail();

        return view('sibel.results.print', compact('attempt', 'result') + [
            'backUrl' => route('sibel.results.show', $result),
        ]);
    }

    /**
     * Rekapitulasi kelas: distribusi kategori + tabel per siswa.
     */
    public function recap()
    {
        Gate::authorize('viewAny', SibelResult::class);

        $latestAttempts = $this->latestCompletedAttempts();

        return view('sibel.recap.index', $this->recapData($latestAttempts));
    }

    /**
     * Rekapitulasi siap cetak.
     */
    public function recapPrint()
    {
        Gate::authorize('viewAny', SibelResult::class);

        $latestAttempts = $this->latestCompletedAttempts();

        return view('sibel.recap.print', $this->recapData($latestAttempts) + [
            'backUrl' => route('sibel.recap.index'),
        ]);
    }

    /**
     * Sesi selesai terakhir per siswa, difilter sesuai parameter rekap
     * (kelas, periode pengisian, pencarian nama).
     *
     * @return Collection<int, SibelAttempt>
     */
    protected function latestCompletedAttempts()
    {
        $classId = request('class_id');
        $period = request('period'); // format YYYY-MM
        $search = request('q');

        $attempts = SibelAttempt::query()
            ->with(['result', 'student.schoolClass'])
            ->where('status', SibelAttempt::STATUS_COMPLETED)
            ->when($classId, fn ($q) => $q->whereHas('student', fn ($s) => $s->where('class_id', $classId)))
            ->when($period, function ($q) use ($period) {
                [$year, $month] = array_map('intval', explode('-', $period));
                $start = sprintf('%04d-%02d-01', $year, $month);
                $end = date('Y-m-t', strtotime($start));

                $q->whereBetween('completed_at', [$start.' 00:00:00', $end.' 23:59:59']);
            })
            ->orderByDesc('id')
            ->get()
            ->unique('student_id')
            ->values()
            ->filter(fn (SibelAttempt $attempt) => $attempt->result !== null);

        if ($search) {
            $attempts = $attempts->filter(
                fn (SibelAttempt $attempt) => str_contains(
                    mb_strtolower($attempt->student->full_name ?? ''),
                    mb_strtolower($search)
                )
            )->values();
        }

        return $attempts;
    }

    /**
     * Susun data rekap: tabel siswa, hitungan & persentase per kategori,
     * serta data grafik distribusi.
     *
     * @param  Collection<int, SibelAttempt>  $attempts
     * @return array<string, mixed>
     */
    protected function recapData($attempts): array
    {
        $distribution = array_fill_keys(array_keys(config('sibel.categories')), 0);
        $ties = 0;

        foreach ($attempts as $attempt) {
            $categories = $attempt->result->dominant_categories ?? [];

            if ($attempt->result->isTie() || count($categories) !== 1) {
                $ties++;

                continue;
            }

            if (array_key_exists($categories[0], $distribution)) {
                $distribution[$categories[0]]++;
            }
        }

        $total = $attempts->count();
        $percentages = array_map(
            fn (int $count) => $total > 0 ? round(($count / $total) * 100, 1) : 0.0,
            $distribution
        );

        $chart = [
            'labels' => [...array_map(fn ($c) => $c['label'], array_values(config('sibel.categories'))), 'Seri'],
            'values' => [...array_values($distribution), $ties],
            'colors' => [...array_map(fn ($c) => $c['hex'], array_values(config('sibel.categories'))), '#64748b'],
        ];

        return [
            'attempts' => $attempts,
            'classes' => SibelClass::orderBy('name')->get(),
            'distribution' => $distribution,
            'percentages' => $percentages,
            'ties' => $ties,
            'total' => $total,
            'chart' => $chart,
        ];
    }
}
