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
                <h3 class="font-semibold text-slate-800">Biodata Siswa</h3>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $ppdb->statusColor() }}">{{ $ppdb->statusLabel() }}</span>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <div class="flex items-center gap-4 sm:col-span-2">
                    @if ($ppdb->photo)
                        <img src="{{ route('admin.ppdb.document', [$ppdb, 'photo']) }}" class="h-20 w-20 rounded-xl object-cover" alt="Foto {{ $ppdb->full_name }}">
                    @else
                        <span class="flex h-20 w-20 items-center justify-center rounded-xl bg-slate-100 text-2xl font-bold text-slate-400">{{ mb_substr($ppdb->full_name, 0, 1) }}</span>
                    @endif
                    <div>
                        <p class="text-lg font-semibold text-slate-800">{{ $ppdb->full_name }}</p>
                        <p class="text-sm text-slate-500">{{ $ppdb->registration_number }} &middot; {{ $ppdb->academic_year ?? '-' }}</p>
                    </div>
                </div>
                <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">Jenis Kelamin</span><span class="font-medium text-slate-700">{{ $ppdb->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</span></div>
                <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">Tempat, Tgl Lahir</span><span class="font-medium text-slate-700">{{ $ppdb->birth_place }}, {{ $ppdb->birth_date?->translatedFormat('d M Y') ?? '-' }}</span></div>
                <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">Agama</span><span class="font-medium text-slate-700">{{ $ppdb->religion ?? '-' }}</span></div>
                <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">NISN</span><span class="font-medium text-slate-700">{{ $ppdb->nisn ?? '-' }}</span></div>
                <div class="text-sm sm:col-span-2"><span class="block text-xs uppercase tracking-wider text-slate-400">Alamat</span><span class="font-medium text-slate-700">{{ $ppdb->address ?? '-' }}</span></div>
                <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">No. HP / WA</span><span class="font-medium text-slate-700">{{ $ppdb->phone ?? '-' }}</span></div>
                <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">Email</span><span class="font-medium text-slate-700">{{ $ppdb->email ?? '-' }}</span></div>
                <div class="text-sm sm:col-span-2"><span class="block text-xs uppercase tracking-wider text-slate-400">Asal Sekolah</span><span class="font-medium text-slate-700">{{ $ppdb->origin_school ?? '-' }}</span></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="font-semibold text-slate-800">Data Orang Tua</h3>
            </div>
            <div class="grid gap-4 p-6 sm:grid-cols-2">
                <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">Nama Ayah</span><span class="font-medium text-slate-700">{{ $ppdb->father_name ?? '-' }}</span></div>
                <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">Pekerjaan Ayah</span><span class="font-medium text-slate-700">{{ $ppdb->father_job ?? '-' }}</span></div>
                <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">Nama Ibu</span><span class="font-medium text-slate-700">{{ $ppdb->mother_name ?? '-' }}</span></div>
                <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">Pekerjaan Ibu</span><span class="font-medium text-slate-700">{{ $ppdb->mother_job ?? '-' }}</span></div>
                <div class="text-sm"><span class="block text-xs uppercase tracking-wider text-slate-400">Penghasilan Keluarga</span><span class="font-medium text-slate-700">{{ $ppdb->family_income ?? '-' }}</span></div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="font-semibold text-slate-800">Dokumen Persyaratan</h3>
            </div>
            <div class="space-y-2 p-6">
                @php
                    $documents = $ppdb->documents->pluck('file_path', 'type');
                    $docLabels = [
                        'kk' => 'Kartu Keluarga (KK)',
                        'birth_certificate' => 'Akta Kelahiran',
                        'diploma' => 'Ijazah / SKL',
                        'report_card' => 'Rapor',
                        'other' => 'Dokumen Lain',
                    ];
                @endphp
                @foreach ($docLabels as $type => $label)
                    @if (isset($documents[$type]) || $type === 'other')
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
                            @if (isset($documents[$type]))
                                <a href="{{ route('admin.ppdb.document', [$ppdb, $type]) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700 hover:text-emerald-800">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    Unduh
                                </a>
                            @else
                                <span class="text-sm text-slate-400">Belum diunggah</span>
                            @endif
                        </div>
                    @endif
                @endforeach
                @if ($ppdb->documents->where('type', '!=', 'kk')->where('type', '!=', 'birth_certificate')->where('type', '!=', 'diploma')->where('type', '!=', 'report_card')->count())
                    <div class="mt-2 rounded-xl border border-dashed border-slate-200 p-3">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Dokumen Lain</p>
                        <div class="space-y-2">
                            @foreach ($ppdb->documents as $doc)
                                @if (!in_array($doc->type, ['kk', 'birth_certificate', 'diploma', 'report_card']))
                                    <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                                        <span class="text-sm text-slate-600">{{ $doc->name }}</span>
                                        <a href="{{ route('admin.ppdb.document', [$ppdb, $doc->type]) }}" target="_blank" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">Unduh</a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
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
