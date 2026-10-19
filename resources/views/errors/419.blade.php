<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sesi Berakhir —419</title>
    <link rel="icon" href="{{ asset_v('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset_v('favicon-32x32.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-900 via-teal-800 to-emerald-950 font-sans text-slate-700 antialiased">
    <main class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <div class="w-full max-w-md rounded-2xl border border-white/10 bg-white p-8 text-center shadow-2xl shadow-emerald-950/40">
            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50">
                <svg class="h-9 w-9 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5m6-2.5a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </span>

            <p class="mt-5 text-xs font-bold uppercase tracking-widest text-emerald-600">Kode419 &middot; Sesi Berakhir</p>
            <h1 class="mt-2 text-xl font-extrabold text-slate-800">Formulir Sudah Kedaluwarsa</h1>
            <p class="mt-2 text-sm leading-relaxed text-slate-500">
                Untuk keamanan, sesi Anda telah berakhir sehingga formulir tidak bisa dikirim.
                Hal ini biasanya terjadi jika halaman dibiarkan terlalu lama atau Anda sempat
                keluar dari akun di tab lain.
            </p>

            <div class="mt-6 flex flex-col gap-3">
                <button type="button" onclick="location.reload()"
                        class="w-full rounded-lg bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600 px-4 py-3 text-sm font-extrabold uppercase tracking-wide text-white shadow-md shadow-emerald-500/30 transition hover:brightness-105">
                    Muat Ulang &amp; Coba Lagi
                </button>
                <a href="{{ url('/') }}"
                   class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-100">
                    Kembali ke Beranda
                </a>
            </div>

            <p class="mt-5 text-xs text-slate-400">
                Masih berulang? Buka lagi halamannya dari awal, lalu isi formulir sekali lagi.
            </p>
        </div>
    </main>
</body>
</html>
