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

        $stats = [
            'news' => $tableExists('news') ? News::count() : 0,
            'teachers' => $tableExists('teachers') ? Teacher::count() : 0,
            'ppdb' => $tableExists('ppdb') ? Ppdb::count() : 0,
            'announcements' => $tableExists('announcements') ? Announcement::count() : 0,
            'visitors' => $tableExists('visitor_logs') ? VisitorLog::count() : 0,
        ];

        $ppdbChart = $tableExists('ppdb')
            ? Ppdb::query()
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray()
            : [];

        $visitorChart = $tableExists('visitor_logs')
            ? VisitorLog::query()
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as day'), DB::raw('count(*) as total'))
                ->where('created_at', '>=', now()->subDays(14))
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total', 'day')
                ->toArray()
            : [];

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
