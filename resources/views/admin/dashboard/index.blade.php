@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Welcome --}}
    <div class="overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-800 to-teal-800 p-6 text-white shadow-lg sm:p-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm text-emerald-200">Selamat datang kembali,</p>
                <h1 class="mt-1 text-2xl font-bold">{{ auth()->user()->name }} 👋</h1>
                <p class="mt-2 max-w-xl text-sm text-emerald-100">
                    Kelola seluruh konten website {{ $settings['site_name'] ?? config('app.name') }} dengan mudah dari panel administrasi ini.
                </p>
            </div>
            <div class="hidden sm:block">
                <svg class="h-24 w-24 text-emerald-400/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M8 17V9m4 8V5m4 12v-6" /></svg>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
        <x-admin.stat-card label="Berita" :value="$stats['news']" icon="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7z" color="emerald" href="{{ Route::has('admin.news.index') ? route('admin.news.index') : '#' }}" />
        <x-admin.stat-card label="Guru & Staff" :value="$stats['teachers']" icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" color="sky" href="#" />
        <x-admin.stat-card label="Pendaftar PPDB" :value="$stats['ppdb']" icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" color="amber" href="{{ Route::has('admin.ppdb.index') ? route('admin.ppdb.index') : '#' }}" />
        <x-admin.stat-card label="Pengumuman" :value="$stats['announcements']" icon="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" color="rose" href="{{ Route::has('admin.announcements.index') ? route('admin.announcements.index') : '#' }}" />
        <x-admin.stat-card label="Pengunjung" :value="$stats['visitors']" icon="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" color="violet" href="#" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- PPDB status --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-1">
            <h3 class="text-sm font-semibold text-slate-800">Status Pendaftar PPDB</h3>
            <div class="mt-4 space-y-3">
                @php
                    $statuses = ['pending' => 'Menunggu Verifikasi', 'verified' => 'Terverifikasi', 'lulus_administrasi' => 'Lulus Administrasi', 'rejected' => 'Ditolak', 'accepted' => 'Diterima'];
                    $colors = ['pending' => 'bg-amber-100 text-amber-700', 'verified' => 'bg-sky-100 text-sky-700', 'lulus_administrasi' => 'bg-indigo-100 text-indigo-700', 'rejected' => 'bg-red-100 text-red-700', 'accepted' => 'bg-emerald-100 text-emerald-700'];
                @endphp
                @foreach ($statuses as $key => $label)
                    <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5">
                        <span class="inline-flex items-center gap-2 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $colors[$key] }}">
                            {{ $label }}
                        </span>
                        <span class="text-sm font-bold text-slate-800">{{ $ppdbByStatus[$key] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PPDB chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-800">Grafik Pendaftar (6 Bulan)</h3>
            <div class="mt-4 flex h-48 items-end gap-2">
                @forelse ($ppdbChart as $month => $total)
                    @php
                        $max = max(array_values($ppdbChart) ?: [1]);
                        $height = $max > 0 ? round(($total / $max) * 100) : 0;
                        $label = \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('M');
                    @endphp
                    <div class="flex flex-1 flex-col items-center gap-1">
                        <span class="text-[10px] font-semibold text-slate-600">{{ $total }}</span>
                        <div class="w-full rounded-t-lg bg-gradient-to-t from-emerald-600 to-emerald-400 transition-all hover:from-emerald-700" style="height: {{ $height }}%"></div>
                        <span class="text-[10px] text-slate-500">{{ $label }}</span>
                    </div>
                @empty
                    <div class="flex h-full w-full items-center justify-center text-sm text-slate-400">Belum ada data</div>
                @endforelse
            </div>
        </div>

        {{-- Visitor chart --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-800">Pengunjung (14 Hari)</h3>
            <div class="mt-4 flex h-48 items-end gap-1">
                @forelse ($visitorChart as $day => $total)
                    @php
                        $max = max(array_values($visitorChart) ?: [1]);
                        $height = $max > 0 ? round(($total / $max) * 100) : 0;
                        $label = \Carbon\Carbon::createFromFormat('Y-m-d', $day)->translatedFormat('d');
                    @endphp
                    <div class="group flex flex-1 flex-col items-center gap-1">
                        <span class="text-[9px] font-semibold text-slate-600">{{ $total }}</span>
                        <div class="w-full rounded-t-md bg-gradient-to-t from-teal-600 to-amber-400 transition-all hover:from-teal-700" style="height: {{ $height }}%"></div>
                        <span class="text-[9px] text-slate-500">{{ $label }}</span>
                    </div>
                @empty
                    <div class="flex h-full w-full items-center justify-center text-sm text-slate-400">Belum ada data</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent PPDB --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-slate-800">Pendaftar Terbaru</h3>
            <a href="{{ Route::has('admin.ppdb.index') ? route('admin.ppdb.index') : '#' }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">Lihat semua &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 text-left text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Asal Sekolah</th>
                        <th class="px-5 py-3">NISN</th>
                        <th class="px-5 py-3">Tanggal Daftar</th>
                        <th class="px-5 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentPpdb as $p)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $p->full_name }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $p->origin_school }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $p->nisn }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $p->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $colors[$p->status] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $statuses[$p->status] ?? $p->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada pendaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
