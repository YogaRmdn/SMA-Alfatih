@extends('admin.layouts.app')

@section('title', 'Kelola Formulir PPDB')

@section('content')
<x-admin.page-header title="Kelola Formulir PPDB" subtitle="Atur pertanyaan, pilihan, dan urutan formulir pendaftaran">
    <x-slot:button>
        <a href="{{ route('admin.ppdb-form-fields.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Pertanyaan
        </a>
        <form method="POST" action="{{ route('admin.ppdb-form-fields.reset') }}" onsubmit="return confirm('Kembalikan formulir ke konfigurasi awal? Semua perubahan manual akan hilang.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Reset ke Default
            </button>
        </form>
        <x-admin.delete-all :route="'admin.ppdb-form-fields.delete-all'" message="Semua pertanyaan formulir akan dihapus dan halaman pendaftaran menjadi kosong. Lanjutkan?" />
    </x-slot:button>
</x-admin.page-header>

<div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
    <p class="font-semibold">Cara kerja</p>
    <ul class="mt-1 list-disc space-y-0.5 pl-5 text-blue-800">
        <li>Urutan pertanyaan mengikuti urutan di bawah ini. Gunakan tombol panah untuk memindahkannya, lalu klik <span class="font-semibold">Simpan Urutan</span>.</li>
        <li>Field <span class="font-semibold">wajib</span> memakai tanda bintang. Field yang disembunyikan tidak lagi tampil di formulir, tetapi jawaban pendaftar lama tetap tersimpan.</li>
        <li>Untuk field dropdown atau pilihan, isi opsi satu per baris. Tulis <code>nilai = Label</code> bila nilai simpanannya berbeda dari teks yang dilihat pendaftar.</li>
        <li>Key dan tipe field tidak dapat diubah setelah disimpan karena sudah terikat ke data pendaftar yang terkirim.</li>
    </ul>
</div>

@if ($fields->isEmpty())
    <div class="rounded-2xl border border-slate-200 bg-white py-16 text-center text-slate-400">
        Belum ada pertanyaan formulir. Klik "Reset ke Default" untuk memuat konfigurasi awal.
    </div>
@else
    <div id="ppdb-form-field-list" class="space-y-3">
        @foreach ($fields as $field)
            <div data-order-row data-field-id="{{ $field->id }}"
                class="rounded-2xl border bg-white p-4 shadow-sm transition {{ $field->is_active ? 'border-slate-200' : 'border-slate-100 opacity-60' }}">
                <div class="flex items-start gap-3">
                    <span data-order-badge class="mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">{{ $loop->iteration }}</span>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-semibold text-slate-800">
                                {{ $field->label }}
                                @if ($field->is_required)<span class="text-red-500">*</span>@endif
                            </h3>
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">{{ $field->typeLabel() }}</span>
                            <code class="rounded bg-slate-50 px-1.5 py-0.5 text-[11px] text-slate-500">{{ $field->key }}</code>
                            @if ($field->group_label)
                                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-700">Digabung: {{ $field->group_label }}</span>
                            @endif
                            @unless ($field->is_active)
                                <span class="rounded-full bg-slate-200 px-2 py-0.5 text-[11px] font-medium text-slate-600">Disembunyikan</span>
                            @endunless
                        </div>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $field->width === 'half' ? 'Setengah lebar' : 'Penuh' }}
                            @if ($field->hasOptions() && $field->optionList() !== [])
                                &middot; Opsi: {{ implode(', ', $field->optionList()) }}
                            @endif
                            @if ($field->isFile())
                                &middot; {{ $field->acceptLabel() }} &middot; maks {{ $field->maxKbLabel() }}
                            @endif
                        </p>

                        @if ($field->help_text)
                            <p class="mt-0.5 text-xs text-slate-400">{{ $field->help_text }}</p>
                        @endif
                    </div>

                    <div class="flex shrink-0 flex-wrap items-center justify-end gap-1.5">
                        <button type="button" data-move="up" title="Naikkan urutan"
                            class="rounded-lg border border-slate-300 p-2 text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" /></svg>
                        </button>
                        <button type="button" data-move="down" title="Turunkan urutan"
                            class="rounded-lg border border-slate-300 p-2 text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                        </button>

                        <x-admin.actions :item="$field" route-prefix="admin.ppdb-form-fields" />

                        <form method="POST" action="{{ route('admin.ppdb-form-fields.toggle', $field) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" title="{{ $field->is_active ? 'Sembunyikan dari formulir' : 'Tampilkan di formulir' }}"
                                class="rounded-lg border border-slate-300 p-2 text-slate-600 transition hover:bg-slate-50">
                                @if ($field->is_active)
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                @else
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z" /></svg>
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <form id="ppdb-form-field-order" method="POST" action="{{ route('admin.ppdb-form-fields.reorder') }}" class="mt-4 flex justify-end">
        @csrf
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan Urutan</button>
    </form>
@endif

@push('scripts')
<script>
    (() => {
        const list = document.getElementById('ppdb-form-field-list');
        const orderForm = document.getElementById('ppdb-form-field-order');
        if (!list || !orderForm) return;

        const renumber = () => {
            [...list.children].forEach((row, index) => {
                row.querySelector('[data-order-badge]').textContent = index + 1;
                row.querySelector('[data-move="up"]').disabled = index === 0;
                row.querySelector('[data-move="down"]').disabled = index === list.children.length - 1;
            });
        };

        list.addEventListener('click', (event) => {
            const button = event.target.closest('[data-move]');
            if (!button) return;

            const row = button.closest('[data-order-row]');
            const sibling = button.dataset.move === 'up' ? row.previousElementSibling : row.nextElementSibling;
            if (!sibling) return;

            button.dataset.move === 'up' ? sibling.before(row) : sibling.after(row);
            renumber();
        });

        orderForm.addEventListener('submit', () => {
            orderForm.querySelectorAll('input[name="order[]"]').forEach((input) => input.remove());

            [...list.children].forEach((row) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'order[]';
                input.value = row.dataset.fieldId;
                orderForm.appendChild(input);
            });
        });

        renumber();
    })();
</script>
@endpush
@endsection
