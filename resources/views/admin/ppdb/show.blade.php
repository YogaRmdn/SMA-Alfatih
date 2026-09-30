@extends('admin.layouts.app')

@section('title', 'Detail Pendaftar - '.$ppdb->full_name)

@section('content')
<x-admin.page-header title="{{ $ppdb->full_name }}" subtitle="No. Registrasi: {{ $ppdb->registration_number }}">
    <x-slot:button>
        <a href="{{ route('admin.ppdb.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali
        </a>
    </x-slot:button>
</x-admin.page-header>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="font-semibold text-slate-800">Jawaban Formulir</h3>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $ppdb->statusColor() }}">{{ $ppdb->statusLabel() }}</span>
            </div>

            <div class="border-b border-slate-100 px-6 py-4">
                <div class="flex items-center gap-4">
                    @if ($ppdb->photo)
                        <img src="{{ route('admin.ppdb.document', [$ppdb, 'photo']) }}" class="h-20 w-20 rounded-xl object-cover" alt="Foto {{ $ppdb->full_name }}">
                    @else
                        <span class="flex h-20 w-20 items-center justify-center rounded-xl bg-slate-100 text-2xl font-bold text-slate-400">{{ mb_substr($ppdb->full_name, 0, 1) }}</span>
                    @endif
                    <div>
                        <p class="text-lg font-semibold text-slate-800">{{ $ppdb->full_name }}</p>
                        <p class="text-sm text-slate-500">{{ $ppdb->registration_number }} &middot; {{ $ppdb->academic_year ?? '-' }}</p>
                        <p class="text-xs text-slate-400">Daftar {{ $ppdb->created_at->translatedFormat('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 p-6 sm:grid-cols-2">
                @forelse ($fields as $field)
                    @php
                        $span = $field->type === 'textarea' || $field->width === 'full';
                    @endphp
                    <div @class(['text-sm', 'sm:col-span-2' => $span])>
                        <span class="block text-xs uppercase tracking-wider text-slate-400">{{ $field->label }}</span>

                        @if ($field->isFile())
                            @if ($ppdb->hasDocument($field->key))
                                <a href="{{ route('admin.ppdb.document', [$ppdb, $field->key]) }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center gap-1.5 font-medium text-emerald-700 hover:text-emerald-800">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    {{ $ppdb->document($field->key)->first()?->name }}
                                </a>
                            @else
                                <span class="font-medium text-amber-600">Belum diunggah</span>
                            @endif
                        @else
                            <span class="font-medium text-slate-700">{{ $form->displayValue($ppdb, $field) ?? '-' }}</span>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-400 sm:col-span-2">Formulir belum punya pertanyaan aktif.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="font-semibold text-slate-800">Dokumen Persyaratan</h3>
                <p class="mt-1 text-sm text-slate-500">Dokumen tidak dikirim pendaftar. Prosesi mengunggah dan mencocokkannya di sini.</p>
            </div>

            <div class="space-y-3 p-6">
                @foreach ($uploadable as $type => $label)
                    @php $document = $ppdb->documents->firstWhere('type', $type); @endphp
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-700">{{ $label }}</p>
                            @if ($document)
                                <p class="truncate text-xs text-slate-400">{{ $document->name }}</p>
                            @else
                                <p class="text-xs text-amber-600">Belum diunggah</p>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($document)
                                <a href="{{ route('admin.ppdb.document', [$ppdb, $type]) }}" target="_blank" rel="noopener"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    Lihat
                                </a>
                                <form method="POST" action="{{ route('admin.ppdb.documents.destroy', [$ppdb, $type]) }}"
                                    onsubmit="return confirm('Hapus dokumen {{ $label }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-red-600 shadow-sm transition hover:bg-red-50">Hapus</button>
                                </form>
                            @endif

                            <form method="POST" enctype="multipart/form-data" action="{{ route('admin.ppdb.documents.store', $ppdb) }}"
                                class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="type" value="{{ $type }}">
                                <input type="file" name="file" required accept=".jpg,.jpeg,.png,.pdf"
                                    class="max-w-[10rem] text-xs text-slate-500 file:mr-2 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                                <button type="submit" class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-800">
                                    {{ $document ? 'Ganti' : 'Unggah' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach

                @foreach ($legacyDocuments as $document)
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-dashed border-slate-200 px-4 py-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-600">{{ $document->name ?: Str::headline($document->type) }}</p>
                            <p class="text-xs text-slate-400">Diunggah pendaftar, sudah tidak ada di formulir</p>
                        </div>
                        <a href="{{ route('admin.ppdb.document', [$ppdb, $document->type]) }}" target="_blank" rel="noopener"
                            class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">Lihat</a>
                    </div>
                @endforeach

                <p class="pt-1 text-xs text-slate-400">Format JPG, PNG, atau PDF maksimal 4 MB per berkas.</p>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="font-semibold text-slate-800">Data Verifikasi Manual</h3>
                <p class="mt-1 text-sm text-slate-500">Diisi panitia dari dokumen fisik atau hasil cek manual.</p>
            </div>

            <form method="POST" action="{{ route('admin.ppdb.details', $ppdb) }}" class="grid gap-5 p-6 sm:grid-cols-2">
                @csrf
                @method('PUT')

                <x-admin.field label="NISN" name="nisn">
                    <x-admin.input name="nisn" :value="$ppdb->nisn" placeholder="10 digit NISN" />
                </x-admin.field>
                <x-admin.field label="Agama" name="religion">
                    <x-admin.input name="religion" :value="$ppdb->religion" placeholder="Contoh: Islam" />
                </x-admin.field>
                <x-admin.field label="Email" name="email">
                    <x-admin.input name="email" type="email" :value="$ppdb->email" placeholder="email@domain.com" />
                </x-admin.field>
                <x-admin.field label="Penghasilan Keluarga" name="family_income">
                    <x-admin.input name="family_income" :value="$ppdb->family_income" placeholder="Contoh: Rp2.000.000 / bulan" />
                </x-admin.field>
                <x-admin.field label="Nama Ayah" name="father_name">
                    <x-admin.input name="father_name" :value="$ppdb->father_name" />
                </x-admin.field>
                <x-admin.field label="Pekerjaan Ayah" name="father_job">
                    <x-admin.input name="father_job" :value="$ppdb->father_job" />
                </x-admin.field>
                <x-admin.field label="Nama Ibu" name="mother_name">
                    <x-admin.input name="mother_name" :value="$ppdb->mother_name" />
                </x-admin.field>
                <x-admin.field label="Pekerjaan Ibu" name="mother_job">
                    <x-admin.input name="mother_job" :value="$ppdb->mother_job" />
                </x-admin.field>

                <div class="sm:col-span-2">
                    <x-admin.field label="Catatan Admin" name="admin_notes" hint="Tampil di halaman status pendaftar.">
                        <x-admin.textarea name="admin_notes" rows="3" :value="$ppdb->admin_notes" />
                    </x-admin.field>
                </div>

                <div class="sm:col-span-2">
                    <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan Data Verifikasi</button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="font-semibold text-slate-800">Update Status</h3>
            </div>
            <form method="POST" action="{{ route('admin.ppdb.status', $ppdb) }}" class="space-y-4 p-6">
                @csrf
                @method('PUT')
                <x-admin.field label="Status Pendaftaran" name="status">
                    <x-admin.select name="status" :options="[
                        'pending' => 'Menunggu Verifikasi',
                        'verified' => 'Terverifikasi',
                        'lulus_administrasi' => 'Lulus Administrasi',
                        'accepted' => 'Diterima',
                        'rejected' => 'Ditolak',
                    ]" :value="$ppdb->status" />
                </x-admin.field>
                <x-admin.field label="Catatan Admin">
                    <x-admin.textarea name="admin_notes" rows="3" :value="$ppdb->admin_notes ?? ''" placeholder="Catatan untuk Pendaftar (Opsional)" />
                </x-admin.field>
                <button type="submit" class="w-full rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan Status</button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-800">Formulir Aktif</h3>
            <p class="mt-1 text-sm text-slate-500">Ubah pertanyaan pendaftaran kapan saja.</p>
            <a href="{{ route('admin.ppdb-form-fields.index') }}"
                class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.11v1.093c0 .551-.398 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.78.93l-.15.894c-.09.542-.56.94-1.11.94h-1.093c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.425-.385-.765-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.11v-1.093c0-.55.398-1.019.94-1.11l.894-.149c.425-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Kelola Formulir
            </a>
        </div>

        <div class="rounded-2xl border border-red-100 bg-red-50 p-6">
            <h3 class="font-semibold text-red-800">Hapus Pendaftaran</h3>
            <p class="mt-1 text-sm text-red-600">Data dan semua dokumen pendaftar akan dihapus permanen.</p>
            <form id="delete-form-{{ $ppdb->id }}" method="POST" action="{{ route('admin.ppdb.destroy', $ppdb) }}" class="mt-4">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmDelete('delete-form-{{ $ppdb->id }}')"
                    class="w-full rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-100">Hapus Pendaftaran</button>
            </form>
        </div>
    </div>
</div>
@endsection
