<?php

namespace App\Http\Controllers\Sibel;

use App\Http\Controllers\Controller;
use App\Models\SibelAttempt;
use App\Models\SibelClass;
use App\Models\SibelResult;
use App\Models\SibelStudent;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = SibelStudent::where('status', SibelStudent::STATUS_ACTIVE)->count();
        $totalClasses = SibelClass::count();
        $completedAttempts = SibelAttempt::where('status', SibelAttempt::STATUS_COMPLETED)->count();

        // Siswa aktif yang belum pernah menyelesaikan kuesioner apa pun.
        $notCompleted = SibelStudent::where('status', SibelStudent::STATUS_ACTIVE)
            ->whereDoesntHave('attempts', fn ($q) => $q->where('status', SibelAttempt::STATUS_COMPLETED))
            ->count();

        // Hasil terakhir tiap siswa (satu baris per siswa).
        $latestResults = $this->latestResults();

        $distribution = array_fill_keys(array_keys(config('sibel.categories')), 0);
        $ties = 0;

        foreach ($latestResults as $result) {
            $categories = $result->dominant_categories ?? [];

            if ($result->isTie() || count($categories) !== 1) {
                $ties++;

                continue;
            }

            $key = $categories[0];
            if (array_key_exists($key, $distribution)) {
                $distribution[$key]++;
            }
        }

        $assessed = array_sum($distribution) + $ties;

        $chart = [
            'labels' => [...array_map(
                fn ($c) => $c['label'],
                array_values(config('sibel.categories'))
            ), 'Seri'],
            'values' => [...array_values($distribution), $ties],
            'colors' => [...array_map(
                fn ($c) => $c['hex'],
                array_values(config('sibel.categories'))
            ), '#64748b'],
        ];

        $recentAttempts = SibelAttempt::with(['student.schoolClass', 'questionnaire'])
            ->latest('id')
            ->limit(6)
            ->get();

        return view('sibel.dashboard', compact(
            'totalStudents',
            'totalClasses',
            'completedAttempts',
            'notCompleted',
            'distribution',
            'ties',
            'assessed',
            'chart',
            'recentAttempts'
        ));
    }

    /**
     * Hasil terakhir (attempt selesai dengan id terbesar) per siswa.
     *
     * @return array<int, SibelResult>
     */
    protected function latestResults(): array
    {
        $attempts = SibelAttempt::with('result')
            ->where('status', SibelAttempt::STATUS_COMPLETED)
            ->orderByDesc('id')
            ->get();

        $results = [];
        foreach ($attempts as $attempt) {
            if (isset($results[$attempt->student_id])) {
                continue;
            }
            if ($attempt->result) {
                $results[$attempt->student_id] = $attempt->result;
            }
        }

        return array_values($results);
    }
}
