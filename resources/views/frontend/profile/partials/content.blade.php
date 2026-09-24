@if ($page?->content)
    <div class="whitespace-pre-line text-justify text-base leading-relaxed text-slate-700">
        {{ str_replace('{site_name}', $settings['site_name'] ?? config('app.name'), $page->content) }}
    </div>
@else
    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center">
        <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
        <h3 class="mt-4 font-bold text-slate-700">Konten belum tersedia</h3>
        <p class="mt-1 text-sm text-slate-500">Halaman ini sedang disiapkan oleh pengelola sekolah. Silakan kunjungi kembali nanti.</p>
    </div>
@endif