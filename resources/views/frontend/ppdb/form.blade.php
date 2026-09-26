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
            Isi formulir berikut dengan data yang benar dan upload dokumen persyaratan.
            Setelah dikirim, sistem akan menerbitkan <span class="font-semibold text-amber-300">No. Registrasi</span> yang dapat Anda gunakan untuk memantau status pendaftaran.
        </p>
        <div class="mt-6 grid max-w-xl gap-3 sm:grid-cols-3">
            <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3 backdrop-blur">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 text-emerald-950 shadow-sm shadow-amber-500/30">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
                <span class="text-sm">
                    <span class="block font-bold text-white">Langkah 1</span>
                    <span class="block text-xs text-emerald-200">Isi Biodata</span>
                </span>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3 backdrop-blur">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 text-emerald-950 shadow-sm shadow-amber-500/30">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                </span>
                <span class="text-sm">
                    <span class="block font-bold text-white">Langkah 2</span>
                    <span class="block text-xs text-emerald-200">Upload Dokumen</span>
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

        <form method="POST" action="{{ route('ppdb.store') }}" enctype="multipart/form-data" class="space-y-8" x-data="{ file: null }">
            @csrf

            {{-- DATA SISWA --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-700 text-sm font-bold text-white">1</span>
                    <div>
                        <h2 class="font-bold text-emerald-950">Data Siswa</h2>
                        <p class="text-xs text-slate-500">Identitas lengkap calon peserta didik</p>
                    </div>
                </div>
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="full_name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Nama sesuai akta kelahiran">
                        @error('full_name')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="gender" class="mb-1.5 block text-sm font-medium text-slate-700">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select name="gender" id="gender" required
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="L" @selected(old('gender') === 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender') === 'P')>Perempuan</option>
                        </select>
                        @error('gender')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="religion" class="mb-1.5 block text-sm font-medium text-slate-700">Agama</label>
                        <select name="religion" id="religion"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $religion)
                                <option value="{{ $religion }}" @selected(old('religion', 'Islam') === $religion)>{{ $religion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="birth_place" class="mb-1.5 block text-sm font-medium text-slate-700">Tempat Lahir</label>
                        <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place') }}"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Kota/Kabupaten">
                        @error('birth_place')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="birth_date" class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" required
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('birth_date')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="nisn" class="mb-1.5 block text-sm font-medium text-slate-700">NISN</label>
                        <input type="text" name="nisn" id="nisn" value="{{ old('nisn') }}" maxlength="10"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="10 digit (opsional)">
                        @error('nisn')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="origin_school" class="mb-1.5 block text-sm font-medium text-slate-700">Asal Sekolah <span class="text-red-500">*</span></label>
                        <input type="text" name="origin_school" id="origin_school" value="{{ old('origin_school') }}" required
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Nama SMP/MTs asal">
                        @error('origin_school')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="mb-1.5 block text-sm font-medium text-slate-700">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="address" id="address" rows="2" required
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota">{{ old('address') }}</textarea>
                        @error('address')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-1.5 block text-sm font-medium text-slate-700">No. HP / WhatsApp <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="08xxxxxxxxxx">
                        @error('phone')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="email@gmail.com">
                        @error('email')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- DATA ORANG TUA --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-700 text-sm font-bold text-white">2</span>
                    <div>
                        <h2 class="font-bold text-emerald-950">Data Orang Tua / Wali</h2>
                        <p class="text-xs text-slate-500">Informasi orang tua atau wali siswa</p>
                    </div>
                </div>
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    <div>
                        <label for="father_name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Ayah <span class="text-red-500">*</span></label>
                        <input type="text" name="father_name" id="father_name" value="{{ old('father_name') }}" required
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('father_name')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="father_job" class="mb-1.5 block text-sm font-medium text-slate-700">Pekerjaan Ayah</label>
                        <input type="text" name="father_job" id="father_job" value="{{ old('father_job') }}"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('father_job')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="mother_name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Ibu <span class="text-red-500">*</span></label>
                        <input type="text" name="mother_name" id="mother_name" value="{{ old('mother_name') }}" required
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('mother_name')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="mother_job" class="mb-1.5 block text-sm font-medium text-slate-700">Pekerjaan Ibu</label>
                        <input type="text" name="mother_job" id="mother_job" value="{{ old('mother_job') }}"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('mother_job')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="family_income" class="mb-1.5 block text-sm font-medium text-slate-700">Penghasilan Keluarga</label>
                        <select name="family_income" id="family_income"
                            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Pilih penghasilan (opsional)</option>
                            @foreach (['< Rp 1.000.000', 'Rp 1.000.000 - Rp 3.000.000', 'Rp 3.000.000 - Rp 5.000.000', '> Rp 5.000.000'] as $income)
                                <option value="{{ $income }}" @selected(old('family_income') === $income)>{{ $income }}</option>
                            @endforeach
                        </select>
                        @error('family_income')<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- DOKUMEN --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center gap-3 border-b border-slate-100 px-6 py-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-700 text-sm font-bold text-white">3</span>
                    <div>
                        <h2 class="font-bold text-emerald-950">Dokumen Persyaratan</h2>
                        <p class="text-xs text-slate-500">Format JPG/PNG/PDF, maksimal 4 MB per dokumen</p>
                    </div>
                </div>
                <div class="grid gap-5 p-6 sm:grid-cols-2">
                    @php
                        $documentFields = [
                            'photo' => ['label' => 'Foto Siswa', 'hint' => 'Pas foto terbaru 3x4 (JPG/PNG, maks 2 MB)', 'accept' => 'image/*', 'required' => true],
                            'kk' => ['label' => 'Kartu Keluarga (KK)', 'hint' => 'Scan KK', 'accept' => 'image/*,.pdf', 'required' => true],
                            'birth_certificate' => ['label' => 'Akta Kelahiran', 'hint' => 'Scan akta kelahiran', 'accept' => 'image/*,.pdf', 'required' => true],
                            'diploma' => ['label' => 'Ijazah / SKL', 'hint' => 'Ijazah SMP atau Surat Keterangan Lulus', 'accept' => 'image/*,.pdf', 'required' => true],
                            'report_card' => ['label' => 'Rapor', 'hint' => 'Scan rapor semester terakhir', 'accept' => 'image/*,.pdf', 'required' => true],
                        ];
                    @endphp
                    @foreach ($documentFields as $name => $doc)
                        <div class="@if ($name === 'photo') sm:col-span-2 @endif">
                            <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-slate-700">
                                {{ $doc['label'] }} @if ($doc['required'])<span class="text-red-500">*</span>@endif
                            </label>
                            <div class="flex flex-col gap-2">
                                @if ($name === 'photo')
                                    <div class="flex items-center gap-4">
                                        <span class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-dashed border-slate-300 bg-slate-50 text-slate-300">
                                            <svg x-show="!file" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        </span>
                                        <input type="file" name="photo" id="photo" accept="{{ $doc['accept'] }}" required
                                            class="w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                                    </div>
                                @else
                                    <input type="file" name="{{ $name }}" id="{{ $name }}" accept="{{ $doc['accept'] }}" required
                                        class="w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $doc['hint'] }}</p>
                            @error($name)<p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- SUBMIT --}}
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    <p class="text-sm leading-relaxed text-emerald-900">
                        Pastikan seluruh data dan dokumen sudah benar sebelum mengirim.
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
    </div>
</section>

@endsection