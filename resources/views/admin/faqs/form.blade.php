@extends('admin.layouts.app')

@section('title', isset($faq) ? 'Edit FAQ' : 'Tambah FAQ')

@section('content')
<x-admin.page-header title="{{ isset($faq) ? 'Edit FAQ' : 'Tambah FAQ' }}">
    <x-slot:button>
        <a href="{{ route('admin.faqs.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Kembali</a>
    </x-slot:button>
</x-admin.page-header>

<form method="POST" action="{{ isset($faq) ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" class="mx-auto max-w-2xl space-y-6">
    @csrf
    @isset($faq) @method('PUT') @endisset

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Detail FAQ</h3>
        </div>
        <div class="space-y-5 p-6">
            <x-admin.field label="Pertanyaan" name="question" required>
                <x-admin.input name="question" :value="$faq->question ?? ''" required />
            </x-admin.field>

            <x-admin.field label="Jawaban" name="answer" required>
                <x-admin.textarea name="answer" rows="4" :value="$faq->answer ?? ''" required />
            </x-admin.field>

            <div class="grid gap-5 md:grid-cols-2">
                <x-admin.field label="Urutan">
                    <x-admin.input type="number" name="sort_order" :value="$faq->sort_order ?? 0" />
                </x-admin.field>
                <div class="pt-1">
                    <x-admin.checkbox name="is_active" label="Aktif Tampil di Website" :checked="$faq->is_active ?? true" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.faqs.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-emerald-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-800">Simpan</button>
    </div>
</form>
@endsection
