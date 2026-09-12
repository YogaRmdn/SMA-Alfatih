@props(['title', 'subtitle' => null, 'button' => null])

<div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
    <div>
        <h2 class="text-xl font-bold text-slate-800">{{ $title }}</h2>
        @if ($subtitle)
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($button)
        <div class="flex shrink-0 gap-2">
            {{ $button }}
        </div>
    @endif
</div>
