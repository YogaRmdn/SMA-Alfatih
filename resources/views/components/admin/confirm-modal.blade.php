<div x-data
     x-cloak
     x-show="$store.confirmModal.open"
     @keydown.escape.window="$store.confirmModal.cancel()"
     class="fixed inset-0 z-[90] overflow-y-auto"
     role="dialog" aria-modal="true" aria-labelledby="confirm-modal-title" aria-describedby="confirm-modal-desc"
     style="display: none">

    <div class="flex min-h-full items-center justify-center p-4">
        {{-- Overlay --}}
        <div x-show="$store.confirmModal.open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"
             @click="$store.confirmModal.cancel()"></div>

        {{-- Panel --}}
        <div x-show="$store.confirmModal.open"
             x-init="$nextTick(() => $refs.confirmButton?.focus())"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-3 scale-95"
             class="relative w-full max-w-md rounded-2xl bg-white p-0 shadow-2xl">

            <div class="flex h-3 w-full rounded-t-2xl bg-gradient-to-r from-red-500 via-red-600 to-red-700"></div>

            <div class="px-6 pb-6 pt-5 sm:px-8">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h3 id="confirm-modal-title" class="text-base font-extrabold text-slate-900 sm:text-lg">Konfirmasi Hapus</h3>
                        <p id="confirm-modal-desc" class="mt-1.5 text-sm leading-relaxed text-slate-600">
                            {{ $message ?? 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.' }}
                        </p>
                    </div>
                    <button type="button" @click="$store.confirmModal.cancel()" aria-label="Tutup" class="shrink-0 rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button type="button"
                            @click="$store.confirmModal.cancel()"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300">
                        Batal
                    </button>
                    <button type="button"
                            x-ref="confirmButton"
                            @click="$store.confirmModal.submit()"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-red-600/30 transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>