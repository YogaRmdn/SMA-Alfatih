@extends('frontend.layouts.app')

@section('title', 'Pendaftaran PPDB')
@section('seo_title', 'PPDB '.($settings['ppdb_tahun_ajaran'] ?? '2026/2027').' — '.($settings['site_name'] ?? config('app.name')))
@section('seo_description', 'Pendaftaran Peserta Didik Baru '.($settings['ppdb_tahun_ajaran'] ?? '2026/2027').' '.($settings['site_name'] ?? config('app.name')).' Pekanbaru sudah dibuka. Pendaftaran online gratis, cepat, dan mudah. Daftar sekarang!')
@section('robots', 'index, follow, max-image-preview:large, max-snippet:-1')

@push('head')
{!! breadcrumb_schema([
    ['name' => 'Beranda', 'url' => route('home')],
    ['name' => 'PPDB'],
]) !!}
@endpush

@section('content')

{{-- ============ HEADER ============ --}}
<section class="relative animate-flow-x overflow-hidden bg-[linear-gradient(120deg,#022c1c,#065f46,#0f766e,#134e4a,#6b3a10)] py-16 lg:py-20">
    <div class="aurora -left-20 top-0 h-64 w-64 bg-amber-400/25"></div>
    <div class="aurora bottom-0 right-10 h-72 w-72 bg-teal-300/25" style="animation-delay:-9s"></div>
    <div class="relative mx-auto max-w-7xl px-4 lg:px-6">
        <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-950 shadow-sm shadow-amber-500/30">
            PPDB {{ $academicYear }}
        </span>
        <h1 class="mt-4 text-3xl font-extrabold text-white lg:text-4xl">Formulir Pendaftaran Peserta Didik Baru</h1>
        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-emerald-100/90">
            Isi formulir berikut dengan data yang benar dan unggah bukti pembayaran formulir pendaftaran.
            Setelah dikirim, sistem akan menerbitkan <span class="font-semibold text-amber-300">No. Registrasi</span> yang dapat Anda gunakan untuk memantau status pendaftaran.
        </p>
        <div class="mt-6 grid max-w-xl gap-3 sm:grid-cols-3">
            <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3 backdrop-blur">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 text-emerald-950 shadow-sm shadow-amber-500/30">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
                <span class="text-sm">
                    <span class="block font-bold text-white">Langkah 1</span>
                    <span class="block text-xs text-emerald-200">Isi Data Siswa</span>
                </span>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3 backdrop-blur">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 text-emerald-950 shadow-sm shadow-amber-500/30">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                </span>
                <span class="text-sm">
                    <span class="block font-bold text-white">Langkah 2</span>
                    <span class="block text-xs text-emerald-200">Unggah Bukti Bayar</span>
                </span>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3 backdrop-blur">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 text-emerald-950 shadow-sm shadow-amber-500/30">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </span>
                <span class="text-sm">
                    <span class="block font-bold text-white">Langkah 3</span>
                    <span class="block text-xs text-emerald-200">Terima No. Registrasi</span>
                </span>
            </div>
        </div>
    </div>
</section>

{{-- ============ FORM ============ --}}
<section class="bg-slate-50 py-16 lg:py-20">
    <div class="mx-auto max-w-3xl px-4 lg:px-6">

        @if ($errors->has('closed'))
            <div class="mb-6 rounded-2xl border border-amber-300 bg-amber-50 p-5">
                <p class="text-sm font-semibold text-amber-800">{{ $errors->first('closed') }}</p>
            </div>
        @endif

        @if ($form->fields()->isEmpty())
            <div class="rounded-2xl border border-amber-300 bg-amber-50 p-6 text-center">
                <p class="text-sm font-semibold text-amber-800">Formulir pendaftaran belum disiapkan oleh administrator.</p>
                <p class="mt-1 text-sm text-amber-700">Silakan hubungi sekolah untuk informasi pendaftaran.</p>
            </div>
        @else
        <form method="POST" action="{{ route('ppdb.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            @foreach ($form->rows() as $row)
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-4">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-700 text-xs font-bold text-white">{{ $loop->iteration }}</span>
                        <h2 class="font-bold text-emerald-950">
                            {{ $row['heading'] }} @if ($row['required'])<span class="text-red-500">*</span>@endif
                        </h2>
                    </div>
                    <div class="grid gap-5 p-6 {{ count($row['fields']) > 1 ? 'sm:grid-cols-2' : '' }}">
                        @foreach ($row['fields'] as $field)
                            <div @class(['sm:col-span-2' => $field->width === 'full' || count($row['fields']) === 1])>

                                @if ($row['sub'])
                                    <label for="{{ $field->key }}" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        {{ $field->label }} @if ($field->is_required)<span class="text-red-500">*</span>@endif
                                    </label>
                                @endif

                                @php
                                    $old = old($field->key);
                                    $inputClass = 'w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500';
                                @endphp

                                @switch($field->type)
                                    @case('textarea')
                                        <textarea name="{{ $field->key }}" id="{{ $field->key }}" rows="2"
                                            @if ($field->is_required) required @endif
                                            class="{{ $inputClass }}"
                                            placeholder="{{ $field->placeholder }}">{{ $old }}</textarea>
                                        @break

                                    @case('date')
                                        <input type="date" name="{{ $field->key }}" id="{{ $field->key }}"
                                            value="{{ $old }}"
                                            @if ($field->is_required) required @endif
                                            class="{{ $inputClass }}">
                                        @break

                                    @case('tel')
                                        <input type="tel" name="{{ $field->key }}" id="{{ $field->key }}"
                                            value="{{ $old }}" inputmode="numeric"
                                            placeholder="{{ $field->placeholder ?? '08xxxxxxxxxx' }}"
                                            @if ($field->is_required) required @endif
                                            class="{{ $inputClass }}">
                                        @break

                                    @case('select')
                                        <select name="{{ $field->key }}" id="{{ $field->key }}"
                                            @if ($field->is_required) required @endif
                                            class="{{ $inputClass }}">
                                            <option value="">{{ $field->is_required ? 'Pilih salah satu' : 'Pilih (opsional)' }}</option>
                                            @foreach ($field->optionList() as $value => $label)
                                                <option value="{{ $value }}" @selected($old === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @break

                                    @case('radio')
                                        <div class="grid gap-2 @if (count($field->optionList()) > 3) 'sm:grid-cols-3' @endif">
                                            @foreach ($field->optionList() as $value => $label)
                                                <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm transition hover:border-emerald-400 hover:bg-emerald-50 has-checked:border-emerald-600 has-checked:bg-emerald-50">
                                                    <input type="radio" name="{{ $field->key }}" value="{{ $value }}"
                                                        @checked($old === $value)
                                                        @if ($field->is_required) required @endif
                                                        class="border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                                    <span class="font-medium text-slate-700">{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @break

                                    @case('file')
                                        <input type="file" name="{{ $field->key }}" id="{{ $field->key }}"
                                            accept="{{ $field->htmlAccept() }}"
                                            @if ($field->is_required) required @endif
                                            class="w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                                        @break

                                    @default
                                        <input type="text" name="{{ $field->key }}" id="{{ $field->key }}"
                                            value="{{ $old }}"
                                            placeholder="{{ $field->placeholder }}"
                                            @if ($field->is_required) required @endif
                                            class="{{ $inputClass }}">
                                @endswitch

                                @if ($field->help_text)
                                    <p class="mt-1 text-xs text-slate-500">{{ $field->help_text }}</p>
                                @elseif ($field->isFile())
                                    <p class="mt-1 text-xs text-slate-500">Format {{ $field->acceptLabel() }}, maksimal {{ $field->maxKbLabel() }}.</p>
                                @endif

                                @error($field->key)
                                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- SUBMIT --}}
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    <p class="text-sm leading-relaxed text-emerald-900">
                        Pastikan seluruh data sudah benar sebelum mengirim.
                        Setelah dikirim, Anda akan mendapat <span class="font-semibold">No. Registrasi</span> otomatis.
                        Simpan nomor tersebut untuk <a href="{{ route('ppdb.status') }}" class="font-semibold underline decoration-emerald-400 underline-offset-2 hover:text-emerald-700">cek status pendaftaran</a>.
                    </p>
                </div>
                <button type="submit"
                    class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-700/30 transition hover:bg-emerald-800">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Daftar Sekarang
                </button>
            </div>
        </form>
        @endif
    </div>
</section>

@endsection
