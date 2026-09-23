@php
    $successMessage = session('success');
    $errorMessage = session('error');
@endphp

@if ($successMessage || $errorMessage)
    <style>
        @keyframes toast-progress {
            from { width: 100%; }
            to { width: 0%; }
        }
        .toast-progress {
            animation: toast-progress 5s linear forwards;
        }
        @media (prefers-reduced-motion: reduce) {
            .toast-progress { animation: none; }
        }
    </style>

    <div class="pointer-events-none fixed inset-x-4 top-4 z-[100] flex flex-col items-stretch gap-3 sm:inset-x-auto sm:right-5 sm:top-5 sm:items-end">
        @if ($successMessage)
            <div x-data="{ open: true }"
                 x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-8 opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="translate-x-8 opacity-0"
                 x-init="setTimeout(() => open = false, 5000)"
                 role="alert"
                 class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-xl border border-emerald-200 bg-white shadow-xl shadow-emerald-900/10">
                <div class="flex items-start gap-3 border-b border-emerald-100 bg-emerald-50/70 px-4 py-3.5">
                    <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white shadow-sm shadow-emerald-600/40">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-emerald-900">Berhasil!</p>
                        <p class="mt-0.5 text-sm leading-snug text-emerald-800">{{ $successMessage }}</p>
                    </div>
                    <button type="button" @click="open = false" aria-label="Tutup Notifikasi" class="shrink-0 rounded-md p-1 text-emerald-400 transition hover:bg-emerald-100 hover:text-emerald-800">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="h-1 w-full overflow-hidden bg-emerald-100">
                    <div class="toast-progress h-full bg-emerald-500"></div>
                </div>
            </div>
        @endif

        @if ($errorMessage)
            <div x-data="{ open: true }"
                 x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-8 opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0 opacity-100"
                 x-transition:leave-end="translate-x-8 opacity-0"
                 x-init="setTimeout(() => open = false, 5000)"
                 role="alert"
                 class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-xl border border-red-200 bg-white shadow-xl shadow-red-900/10">
                <div class="flex items-start gap-3 border-b border-red-100 bg-red-50/70 px-4 py-3.5">
                    <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-red-600 text-white shadow-sm shadow-red-600/40">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM12 3v.008h.008V4.5m0 .008a8.992 8.992 0 100 17.984 8.992 8.992 0 000-17.984z" /></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-red-900">Gagal!</p>
                        <p class="mt-0.5 text-sm leading-snug text-red-800">{{ $errorMessage }}</p>
                    </div>
                    <button type="button" @click="open = false" aria-label="Tutup Notifikasi" class="shrink-0 rounded-md p-1 text-red-400 transition hover:bg-red-100 hover:text-red-800">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="h-1 w-full overflow-hidden bg-red-100">
                    <div class="toast-progress h-full bg-red-500"></div>
                </div>
            </div>
        @endif
    </div>
@endif