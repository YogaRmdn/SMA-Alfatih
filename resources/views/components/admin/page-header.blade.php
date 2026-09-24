@props(['title', 'subtitle' => null, 'button' => null])

<div class="relative mb-6 overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-900 px-5 py-6 shadow-lg shadow-emerald-900/20 sm:px-7 sm:py-7">
    <div class="pointer-events-none absolute -right-10 -top-14 h-36 w-36 rounded-full bg-amber-400/25"></div>
    <div class="pointer-events-none absolute -top-4 right-24 h-20 w-20 rounded-full bg-teal-300/10"></div>
    <div class="pointer-events-none absolute -bottom-16 -left-8 h-32 w-32 rounded-full bg-emerald-400/10"></div>
    <div class="pointer-events-none absolute right-10 top-0 h-px w-32 bg-gradient-to-r from-transparent via-amber-300/60 to-transparent"></div>

    <div class="relative flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-xl font-bold text-white sm:text-2xl">{{ $title }}</h2>
            @if ($subtitle)
                <p class="mt-1.5 max-w-xl text-sm text-emerald-100/90">{{ $subtitle }}</p>
            @endif
        </div>
        @if ($button)
            <div class="grid w-full gap-2 rounded-2xl bg-white/95 p-2 shadow-sm [&>*]:w-full [&>*]:justify-center sm:flex sm:w-auto sm:flex-wrap sm:items-center sm:[&>*]:w-auto sm:[&>*]:justify-start">
                {{ $button }}
            </div>
        @endif
    </div>
</div>