<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public const ACTION_BADGES = [
        'login' => ['label' => 'Login', 'class' => 'bg-emerald-100 text-emerald-700'],
        'logout' => ['label' => 'Logout', 'class' => 'bg-slate-100 text-slate-600'],
        'login_failed' => ['label' => 'Login Gagal', 'class' => 'bg-red-100 text-red-700'],
    ];

    public function index(Request $request)
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = $request->q;
                $q->where(function ($query) use ($search) {
                    $query->where('description', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('action') && $request->action !== 'all', fn ($q) => $q->where('action', $request->action))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.activity-logs.index', compact('logs'));
    }

    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();

        return back()->with('success', 'Log aktivitas berhasil dihapus.');
    }

    public function clearAll()
    {
        ActivityLog::query()->delete();

        return back()->with('success', 'Semua log aktivitas berhasil dibersihkan.');
    }
}