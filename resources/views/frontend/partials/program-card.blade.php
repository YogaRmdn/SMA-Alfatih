@props([
    'name' => null,
    'excerpt' => null,
    'content' => null,
    'icon' => null,
    'palette' => [],
    'number' => '01',
    'interactive' => false,
    'defaultIcon' => null,
])

<article @if ($interactive) x-data="{ open: false }" @endif
         class="group relative flex h-full flex-col overflow-hidden rounded-3xl bg-white p-7 shadow-[0_12px_40px_-20px_rgba(2,44,34,0.25)] ring-1 ring-slate-900/5 transition duration-300 hover:-translate-y-2 hover:shadow-[0_24px_60px_-24px_rgba(2,44,34,0.4)]">
    <span class="pointer-events-none absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r {{ $palette['bar'] }}"></span>
    <span class="pointer-events-none absolute -right-12 -top-12 h-36 w-36 rounded-full {{ $palette['glow'] }} opacity-0 blur-2xl transition-opacity duration-500 group-hover:opacity-100" aria-hidden="true"></span>
    <span class="pointer-events-none absolute right-5 top-4 select-none text-5xl font-extrabold tracking-tight text-slate-900/[0.06] transition-colors duration-300 {{ $palette['num'] }}" aria-hidden="true">{{ $number }}</span>

    <div class="flex items-center justify-between gap-3">
        <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $palette['tile'] }} text-white shadow-lg transition duration-300 group-hover:rotate-3 group-hover:scale-110">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon ?: $defaultIcon }}" /></svg>
        </span>
        <span class="rounded-full px-3 py-1 text-[12px] font-bold uppercase tracking-wide ring-1 {{ $palette['chip'] }}">{{ $palette['label'] }}</span>
    </div>

    <h3 class="mt-6 text-lg font-extrabold text-emerald-950">{{ $name }}</h3>

    @if ($excerpt)
        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $excerpt }}</p>
    @endif

    @if ($interactive)
        <div class="mt-auto pt-6">
            <button type="button" @click="open = !open" class="group/btn inline-flex items-center gap-2 text-sm font-bold {{ $palette['link'] }} transition">
                <span x-text="open ? 'Tutup Detail' : 'Lihat Detail'">Lihat Detail</span>
                <svg class="h-4 w-4 transition-transform duration-300 group-hover/btn:translate-x-0.5" :class="open ? 'rotate-90' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </button>

            @if ($content)
                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2">
                    <div class="mt-4 rounded-2xl bg-slate-50 p-4 text-sm leading-relaxed text-slate-600 ring-1 ring-slate-900/5">{!! $content !!}</div>
                </div>
            @endif
        </div>
    @endif
</article>