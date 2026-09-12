@props(['label', 'value', 'icon', 'color' => 'emerald', 'href' => null])

@php
    $colors = [
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'sky' => 'bg-sky-50 text-sky-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'rose' => 'bg-rose-50 text-rose-600',
        'violet' => 'bg-violet-50 text-violet-600',
        'teal' => 'bg-teal-50 text-teal-600',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md']) }}>
    @if ($href)
        <a href="{{ $href }}" class="block">
    @endif
        <div class="flex items-center justify-between">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $colors[$color] ?? $colors['emerald'] }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
            </span>
            <svg class="h-4 w-4 text-slate-300 transition group-hover:text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        </div>
        <p class="mt-3 text-2xl font-bold text-slate-800">{{ $value }}</p>
        <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
    @if ($href)
        </a>
    @endif
</div>
