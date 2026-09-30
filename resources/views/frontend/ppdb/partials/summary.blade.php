@php
    $summaryFields = collect($form->fields())
        ->reject(fn ($field) => $field->isFile())
        ->filter(fn ($field) => $form->displayValue($ppdb, $field) !== null);
@endphp

@if ($summaryFields->isNotEmpty())
    <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-left">
        <p class="text-xs font-bold uppercase tracking-wider text-emerald-200">Ringkasan Data Pendaftaran</p>

        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
            @foreach ($summaryFields as $field)
                @php $value = $form->displayValue($ppdb, $field); @endphp
                <div @class(['sm:col-span-2' => mb_strlen((string) $value) > 60 || $field->type === 'textarea'])>
                    <dt class="text-xs uppercase tracking-wider text-emerald-300/70">{{ $field->label }}</dt>
                    <dd class="mt-0.5 text-sm font-medium text-white">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>

        @if ($ppdb->hasDocument('payment_proof'))
            <div class="mt-5 border-t border-white/10 pt-4">
                <p class="text-xs uppercase tracking-wider text-emerald-300/70">Bukti Pembayaran</p>
                <p class="mt-0.5 text-sm font-medium text-emerald-200">Sudah diunggah dan menunggu verifikasi panitia.</p>
            </div>
        @endif
    </div>
@endif
