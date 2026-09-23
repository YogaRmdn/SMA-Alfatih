<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\News;
use App\Models\Ppdb;
use App\Models\Teacher;
use App\Models\VisitorLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $tableExists = fn (string $table) => Schema::hasTable($table);

        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        $dateFormat = function (string $column, string $fmt, string $alias) use ($isSqlite) {
            if ($isSqlite) {
                return DB::raw(sprintf("strftime('%s', %s) as %s", $fmt, $column, $alias));
            }

            return DB::raw(sprintf('DATE_FORMAT(%s, "%s") as %s', $column, $fmt, $alias));
        };

        $stats = [
            'news' => $tableExists('news') ? News::count() : 0,
            'teachers' => $tableExists('teachers') ? Teacher::count() : 0,
            'ppdb' => $tableExists('ppdb') ? Ppdb::count() : 0,
            'announcements' => $tableExists('announcements') ? Announcement::count() : 0,
            'visitors' => $tableExists('visitor_logs') ? VisitorLog::count() : 0,
        ];

        $ppdbChart = $tableExists('ppdb')
            ? Ppdb::query()
                ->select($dateFormat('created_at', '%Y-%m', 'month'), DB::raw('count(*) as total'))
                ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray()
            : [];

        // Pastikan semua 6 bulan terakhir muncul di grafik, diisi 0 bila kosong
        if ($tableExists('ppdb')) {
            for ($i = 5; $i >= 0; $i--) {
                $key = now()->subMonths($i)->format('Y-m');
                $ppdbChart[$key] = $ppdbChart[$key] ?? 0;
            }
            ksort($ppdbChart);
        }

        $visitorChart = $tableExists('visitor_logs')
            ? VisitorLog::query()
                ->select($dateFormat('created_at', '%Y-%m-%d', 'day'), DB::raw('count(*) as total'))
                ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day')
                ->toArray()
            : [];

        // Pastikan semua 7 hari terakhir muncul di grafik, diisi 0 bila kosong
        if ($tableExists('visitor_logs')) {
            for ($i = 6; $i >= 0; $i--) {
                $key = now()->subDays($i)->format('Y-m-d');
                $visitorChart[$key] = $visitorChart[$key] ?? 0;
            }
            ksort($visitorChart);
        }

        $ppdbByStatus = $tableExists('ppdb')
            ? Ppdb::query()
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray()
            : [];

        $recentPpdb = $tableExists('ppdb')
            ? Ppdb::query()->with('documents')->latest()->limit(5)->get()
            : collect();

        return view('admin.dashboard.index', compact(
            'stats',
            'ppdbChart',
            'visitorChart',
            'ppdbByStatus',
            'recentPpdb'
        ));
    }
}
