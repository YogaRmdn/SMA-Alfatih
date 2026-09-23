@extends('frontend.layouts.app')

@section('title', 'Cek Status Pendaftaran')

@section('content')

{{-- ============ HEADER ============ --}}
<section class="relative animate-flow-x overflow-hidden bg-[linear-gradient(120deg,#022c1c,#065f46,#0f766e,#134e4a,#6b3a10)] py-16 lg:py-20">
    <div class="aurora -left-20 top-0 h-64 w-64 bg-amber-400/25"></div>
    <div class="aurora bottom-0 right-10 h-72 w-72 bg-teal-300/25" style="animation-delay:-9s"></div>
    <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
        <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-950 shadow-sm shadow-amber-500/30">
            PPDB {{ $settings['ppdb_tahun_ajaran'] ?? '' }}
        </span>
        <h1 class="mt-4 text-3xl font-extrabold text-white lg:text-4xl">Cek Status Pendaftaran</h1>
        <p class="mt-3 max-w-xl text-sm leading-relaxed text-emerald-100/90">
            Masukkan No. Registrasi, tanggal lahir, dan kode akses untuk melihat status pendaftaran Anda secara realtime.
        </p>
    </div>
</section>

<section class="bg-slate-50 py-16 lg:py-20">
    <div class="mx-auto max-w-2xl px-4 lg:px-6">

        {{-- Form cek status --}}
        @if (! isset($ppdb))
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <form method="POST" action="{{ route('ppdb.status.check') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="registration_number" class="mb-1.5 block text-sm font-medium text-slate-700">No. Registrasi <span class="text-red-500">*</span></label>
                        <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}" required
                            class="w-full rounded-lg border-slate-300 font-semibold tracking-wide text-slate-800 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Contoh: PPDB-2026-0001">
                        @error('registration_number')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="birth_date" class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" required
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('birth_date')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="access_code" class="mb-1.5 block text-sm font-medium text-slate-700">Kode Akses <span class="text-red-500">*</span></label>
                        <input type="text" name="access_code" id="access_code" value="{{ old('access_code') }}" required
                            class="w-full rounded-lg border-slate-300 font-mono font-semibold tracking-widest text-slate-800 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Contoh: 7KD2P9XA" maxlength="16">
                        <p class="mt-1 text-xs text-slate-400">Kode akses ditampilkan pada halaman sukses setelah pendaftaran.</p>
                        @error('access_code')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-700/30 transition hover:bg-emerald-800">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        Cek Status
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-500">
                    Belum mendaftar?
                    <a href="{{ route('ppdb.register') }}" class="font-semibold text-emerald-700 underline decoration-emerald-300 underline-offset-2 hover:text-emerald-800">Daftar sekarang</a>
                </p>
            </div>
        @endif

        {{-- Hasil cek --}}
        @if (isset($ppdb))
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-5">
                    <div>
                        <h2 class="text-lg font-bold text-emerald-950">Hasil Pencarian</h2>
                        <p class="text-sm text-slate-500">{{ $ppdb->registration_number }}</p>
                    </div>
                    <span class="shrink-0 rounded-full px-4 py-1.5 text-xs font-bold {{ $ppdb->statusColor() }}">{{ $ppdb->statusLabel() }}</span>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-2">
                    <div class="flex items-center gap-4 sm:col-span-2">
                        @if ($ppdb->photo)
                            <img src="{{ route('ppdb.document', [$ppdb, 'photo']) }}" alt="{{ $ppdb->full_name }}" class="h-16 w-16 rounded-xl object-cover">
                        @else
                            <span class="flex h-16 w-16 items-center justify-center rounded-xl bg-emerald-100 text-xl font-bold text-emerald-700">{{ mb_substr($ppdb->full_name, 0, 1) }}</span>
                        @endif
                        <div>
                            <p class="font-bold text-emerald-950">{{ $ppdb->full_name }}</p>
                            <p class="text-sm text-slate-500">Tahun Ajaran {{ $ppdb->academic_year ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">Jenis Kelamin</span><span class="font-medium text-slate-700">{{ $ppdb->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span></div>
                    <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">Asal Sekolah</span><span class="font-medium text-slate-700">{{ $ppdb->origin_school ?? '-' }}</span></div>
                </div>

                @if ($ppdb->admin_notes)
                    <div class="mx-6 mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-700">Catatan Panitia</p>
                        <p class="mt-1 text-sm leading-relaxed text-amber-900">{{ $ppdb->admin_notes }}</p>
                    </div>
                @endif

                <div class="flex flex-col gap-2 border-t border-slate-100 px-6 py-4 sm:flex-row">
                    <a href="{{ route('ppdb.status') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Cek Lagi
                    </a>
                    @if ($contact?->whatsapp)
                        <a href="{{ 'https://wa.me/'.preg_replace('/\D+/', '', $contact->whatsapp) }}?text=Halo, saya {{ $ppdb->full_name }} (No. {{ $ppdb->registration_number }}). Mohon info status pendaftaran saya." target="_blank" rel="noopener"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            Tanya via WhatsApp
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>

@endsection