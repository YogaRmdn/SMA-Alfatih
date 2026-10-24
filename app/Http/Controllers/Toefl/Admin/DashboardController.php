<?php

namespace App\Http\Controllers\Toefl\Admin;

use App\Http\Controllers\Controller;
use App\Models\ToeflAttempt;
use App\Models\ToeflExam;
use App\Models\ToeflResult;
use App\Models\ToeflStudent;

class DashboardController extends Controller
{
    public function index()
    {
        $totalExams = ToeflExam::count();
        $publishedExams = ToeflExam::where('status', ToeflExam::STATUS_PUBLISHED)->count();
        $totalStudents = ToeflStudent::where('status', ToeflStudent::STATUS_ACTIVE)->count();

        $activeAttempts = ToeflAttempt::where('status', ToeflAttempt::STATUS_IN_PROGRESS)->count();
        $finishedAttempts = ToeflAttempt::whereIn('status', [
            ToeflAttempt::STATUS_SUBMITTED,
            ToeflAttempt::STATUS_EXPIRED,
        ])->count();

        $totalResults = ToeflResult::count();

        // Rata-rata skor scaled (bila sudah ada hasil).
        $avgScaled = $totalResults > 0
            ? (int) round(ToeflResult::avg('scaled_score'))
            : 0;

        // Sesi yang sedang berjalan (untuk pemantauan pengawas).
        $liveAttempts = ToeflAttempt::with(['student', 'exam'])
            ->where('status', ToeflAttempt::STATUS_IN_PROGRESS)
            ->whereNotNull('started_at')
            ->latest('id')
            ->limit(8)
            ->get();

        // Attempt terakhir yang selesai + hasilnya.
        $recentResults = ToeflResult::with(['attempt.student', 'attempt.exam'])
            ->latest('id')
            ->limit(8)
            ->get();

        // Distribusi skor scaled ke pita (untuk grafik).
        $bands = [
            '310–399' => 0,
            '400–479' => 0,
            '480–559' => 0,
            '560–619' => 0,
            '620–680' => 0,
        ];

        foreach (ToeflResult::pluck('scaled_score') as $score) {
            if ($score < 400) {
                $bands['310–399']++;
            } elseif ($score < 480) {
                $bands['400–479']++;
            } elseif ($score < 560) {
                $bands['480–559']++;
            } elseif ($score < 620) {
                $bands['560–619']++;
            } else {
                $bands['620–680']++;
            }
        }

        $chart = [
            'labels' => array_keys($bands),
            'values' => array_values($bands),
            'colors' => ['#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#059669'],
        ];

        return view('toefl.admin.dashboard', compact(
            'totalExams',
            'publishedExams',
            'totalStudents',
            'activeAttempts',
            'finishedAttempts',
            'totalResults',
            'avgScaled',
            'liveAttempts',
            'recentResults',
            'chart'
        ));
    }
}
