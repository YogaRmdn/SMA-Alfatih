@props(['label', 'value', 'icon', 'color' => 'emerald', 'href' => null])

@php
    $cards = [
        'emerald' => ['from-emerald-600 via-emerald-600 to-teal-700', 'shadow-emerald-900/20'],
        'amber'   => ['from-amber-500 via-amber-500 to-orange-600', 'shadow-orange-900/20'],
        'orange'  => ['from-orange-500 via-orange-500 to-orange-700', 'shadow-orange-900/20'],
        'rose'    => ['from-rose-600 via-rose-600 to-pink-700', 'shadow-rose-900/20'],
        'sky'     => ['from-sky-600 via-sky-600 to-blue-700', 'shadow-blue-900/20'],
        'violet'  => ['from-violet-600 via-violet-600 to-purple-700', 'shadow-purple-900/20'],
        'teal'    => ['from-teal-600 via-teal-600 to-cyan-700', 'shadow-teal-900/20'],
    ];
    [$gradient, $shadow] = $cards[$color] ?? $cards['emerald'];
@endphp

<div {{ $attributes->merge(['class' => "group relative flex flex-col overflow-hidden rounded-2xl bg-gradient-to-br $gradient p-4 text-white shadow-lg $shadow transition duration-300 hover:-translate-y-1 hover:shadow-xl sm:p-5"]) }}>
    <div class="pointer-events-none absolute -right-10 -top-12 h-28 w-28 rounded-full bg-white/10"></div>
    <div class="pointer-events-none absolute -bottom-14 -right-6 h-24 w-24 rounded-full bg-black/10"></div>
    <div class="pointer-events-none absolute right-8 top-0 h-px w-24 bg-gradient-to-r from-transparent to-white/40"></div>

    @if ($href)
        <a href="{{ $href }}" class="flex flex-1 flex-col">
    @endif
        <div class="relative flex items-start justify-between">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-white ring-1 ring-white/30 backdrop-blur-sm sm:h-11 sm:w-11">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
            </span>
            @if ($href)
                <svg class="h-4 w-4 text-white/50 transition group-hover:translate-x-0.5 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            @endif
        </div>
        <p class="relative mt-3 text-xl font-extrabold tabular-nums sm:text-2xl">{{ $value }}</p>
        <p class="relative mt-0.5 text-xs font-medium leading-snug text-white/80">{{ $label }}</p>
    @if ($href)
        </a>
    @endif
</div>