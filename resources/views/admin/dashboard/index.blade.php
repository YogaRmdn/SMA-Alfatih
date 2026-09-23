@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="animate-flow-y space-y-6 rounded-3xl bg-gradient-to-br from-amber-100 via-orange-100 to-emerald-100 p-4 sm:p-6">
    {{-- Welcome --}}
    <div class="animate-flow-x overflow-hidden rounded-2xl bg-gradient-to-r from-amber-500 via-orange-500 to-emerald-600 p-6 text-white shadow-lg sm:p-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm text-amber-100">Selamat datang kembali,</p>
                <h1 class="mt-1 text-2xl font-bold">{{ auth()->user()->name }} 👋</h1>
                <p class="mt-2 max-w-xl text-sm text-amber-50">
                    Kelola seluruh konten website {{ $settings['site_name'] ?? config('app.name') }} dengan mudah dari panel administrasi ini.
                </p>
            </div>
            <img src="{{ img_url($settings['logo'] ?? null, 'img/sma.png') }}" alt="Logo {{ $settings['site_name'] ?? 'SMA IT Tahfizh Al-Fatih Pekanbaru' }}"
                 class="h-14 w-14 rounded-xl bg-white/20 p-2 object-contain shadow-lg ring-1 ring-white/30 sm:h-24 sm:w-24 sm:rounded-2xl">
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-5">
        <x-admin.stat-card label="Berita" :value="$stats['news']" icon="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7z" color="emerald" href="{{ Route::has('admin.news.index') ? route('admin.news.index') : '#' }}" />
        <x-admin.stat-card label="Guru & Staff" :value="$stats['teachers']" icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" color="amber" href="#" />
        <x-admin.stat-card label="Pendaftar PPDB" :value="$stats['ppdb']" icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" color="orange" href="{{ Route::has('admin.ppdb.index') ? route('admin.ppdb.index') : '#' }}" />
        <x-admin.stat-card label="Pengumuman" :value="$stats['announcements']" icon="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" color="emerald" href="{{ Route::has('admin.announcements.index') ? route('admin.announcements.index') : '#' }}" />
        <x-admin.stat-card label="Pengunjung" :value="$stats['visitors']" icon="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" color="orange" href="#" class="col-span-2 sm:col-span-1" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- PPDB status --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-1">
            <div class="flex items-center justify-between border-b border-slate-100 bg-gradient-to-r from-amber-50 to-white px-5 py-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Status Pendaftar PPDB</h3>
                    <p class="text-xs text-slate-400">Rincian berdasarkan status</p>
                </div>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-md">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>
                </span>
            </div>
            <div class="space-y-2.5 p-5">
                @php
                    $statuses = ['pending' => 'Menunggu Verifikasi', 'verified' => 'Terverifikasi', 'lulus_administrasi' => 'Lulus Administrasi', 'rejected' => 'Ditolak', 'accepted' => 'Diterima'];
                    $colors = ['pending' => 'bg-amber-100 text-amber-700', 'verified' => 'bg-emerald-100 text-emerald-700', 'lulus_administrasi' => 'bg-orange-100 text-orange-700', 'rejected' => 'bg-red-100 text-red-700', 'accepted' => 'bg-emerald-600 text-white'];
                    $totalPpdb = array_sum($ppdbByStatus ?: [0]);
                @endphp
                @foreach ($statuses as $key => $label)
                    @php
                        $count = $ppdbByStatus[$key] ?? 0;
                        $width = $totalPpdb > 0 ? round(($count / $totalPpdb) * 100) : 0;
                    @endphp
                    <div class="rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-2 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $colors[$key] }}">
                                {{ $label }}
                            </span>
                            <span class="text-sm font-bold text-slate-800">{{ $count }}</span>
                        </div>
                        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-200/70">
                            <div class="h-full rounded-full {{ $colors[$key] }} transition-all duration-500" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PPDB chart bulanan --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 bg-gradient-to-r from-orange-50 to-white px-5 py-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Pendaftar per Bulan</h3>
                    <p class="text-xs text-slate-400">Data 6 bulan terakhir</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-700">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    Total: {{ $totalPpdb }}
                </span>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto">
                    <div class="flex h-44 items-end gap-1.5 sm:h-52 sm:min-w-[280px]">
                    @forelse ($ppdbChart as $month => $total)
                        @php
                            $max = max($ppdbChart) ?: 1;
                            $height = $max > 0 ? max(8, round(($total / $max) * 100)) : 0;
                            $isHighest = $total === max($ppdbChart);
                            $label = \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('M y');
                        @endphp
                        <div class="group flex h-full flex-1 flex-col items-center justify-end gap-1.5">
                            <span class="rounded-md bg-orange-700 px-1.5 py-0.5 text-[11px] font-bold text-white opacity-0 shadow-md transition-opacity duration-200 group-hover:opacity-100">{{ $total }}</span>
                            <div class="flex w-full flex-1 items-end">
                                <div class="relative w-full overflow-hidden rounded-t-xl bg-gradient-to-t from-amber-500 via-orange-500 to-orange-400 shadow-md shadow-orange-500/30 transition-all duration-300 group-hover:brightness-110" style="height: {{ $height }}%">
                                    @if ($isHighest)
                                        <span class="absolute -top-4 left-1/2 -translate-x-1/2 text-[10px] font-bold text-amber-500">&#9650;</span>
                                    @endif
                                </div>
                            </div>
                            <span class="whitespace-nowrap text-[11px] font-semibold text-slate-500">{{ $label }}</span>
                        </div>
                    @empty
                        <div class="flex h-full w-full items-center justify-center text-sm text-slate-400">Belum ada data</div>
                    @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Visitor chart --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-white px-5 py-4">
                <div>
                    <h3 class="text-sm font-semibold text-slate-800">Pengunjung</h3>
                    <p class="text-xs text-slate-400">Data 7 hari terakhir</p>
                </div>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-md">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                </span>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto">
                    <div class="flex h-44 items-end gap-1.5 sm:h-52 sm:min-w-[280px]">
                    @forelse ($visitorChart as $day => $total)
                        @php
                            $all = array_values($visitorChart);
                            $max = max($all) ?: 1;
                            $last7 = array_slice($all, -7);
                            $height = $max > 0 ? max(8, round(($total / $max) * 100)) : 0;
                            $label = \Carbon\Carbon::createFromFormat('Y-m-d', $day)->translatedFormat('D');
                        @endphp
                        <div class="group flex h-full flex-1 flex-col items-center justify-end gap-1.5">
                            <span class="rounded-md bg-emerald-700 px-1.5 py-0.5 text-[11px] font-bold text-white opacity-0 shadow-md transition-opacity duration-200 group-hover:opacity-100">{{ $total }}</span>
                            <div class="flex w-full flex-1 items-end">
                                <div class="w-full rounded-t-xl bg-gradient-to-t from-emerald-600 via-emerald-500 to-emerald-300 shadow-md shadow-emerald-500/30 transition-all duration-300 group-hover:brightness-110" style="height: {{ $height }}%"></div>
                            </div>
                            <span class="whitespace-nowrap text-[11px] font-semibold text-slate-500">{{ $label }}</span>
                        </div>
                    @empty
                        <div class="flex h-full w-full items-center justify-center text-sm text-slate-400">Belum ada data</div>
                    @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent PPDB --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h3 class="text-sm font-semibold text-slate-800">Pendaftar Terbaru</h3>
            <a href="{{ Route::has('admin.ppdb.index') ? route('admin.ppdb.index') : '#' }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">Lihat Semua &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-max text-sm">
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
