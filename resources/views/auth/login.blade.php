<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset_v('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset_v('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset_v('apple-touch-icon.png') }}">
    <meta name="robots" content="noindex, nofollow">
    <title>Masuk — {{ $settings['site_name'] ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-slate-700 antialiased">

    @php
        $siteName = $settings['site_name'] ?? config('app.name');
        $tagline = $settings['site_tagline'] ?? 'Mencetak Generasi Qur\'ani, Berprestasi & Berkarakter';
    @endphp

    <div class="flex min-h-screen">

        {{-- Panel kiri: foto & branding --}}
        <div class="relative hidden w-1/2 overflow-hidden lg:block xl:w-3/5">
            <img src="{{ asset('img/Siang 3.0.png') }}" alt="{{ $siteName }}" class="absolute inset-0 h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-950/95 via-emerald-950/80 to-teal-950/60"></div>
            <div class="relative flex h-full flex-col justify-between p-10 xl:p-14">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('img/sma.png') }}" alt="Logo" class="h-12 w-12 rounded-xl bg-white object-contain p-1">
                    <span class="text-sm font-bold text-white">{{ $siteName }}</span>
                </div>

                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-amber-400 via-amber-500 to-orange-500 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-950 shadow-sm shadow-amber-500/30">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                        Sekolah Islam Terpadu & Tahfizh
                    </span>
                    <h1 class="mt-4 text-3xl font-extrabold leading-tight text-white xl:text-4xl">{{ $siteName }}</h1>
                    <p class="mt-3 max-w-md text-sm leading-relaxed text-emerald-100/90">{{ $tagline }}</p>
                </div>

                <p class="text-xs text-emerald-200/70">&copy; {{ date('Y') }} {{ $siteName }}</p>
            </div>
        </div>

        {{-- Panel kanan: form login --}}
        <div class="flex w-full items-center justify-center bg-slate-50 px-4 py-10 lg:w-1/2 lg:px-10 xl:w-2/5"
             x-data="{ showEmailAlert: false }">
            <div class="w-full max-w-md">

                <div class="mb-8 flex flex-col items-center text-center lg:items-start lg:text-left">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 lg:hidden">
                        <img src="{{ asset('img/sma.png') }}" alt="Logo" class="h-12 w-12 rounded-xl bg-white object-contain p-1 shadow-sm">
                        <span class="text-sm font-bold leading-tight text-emerald-950">{{ $siteName }}</span>
                    </a>
                    <span class="mt-6 inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-slate-200 bg-white p-1.5 shadow-lg shadow-emerald-700/20">
                        <img src="{{ asset('img/sma.png') }}" alt="Logo {{ $siteName }}" class="h-full w-full object-contain">
                    </span>
                    <h2 class="mt-4 text-2xl font-extrabold text-emerald-950">Masuk ke Panel Admin</h2>
                    <p class="mt-1.5 text-sm text-slate-500">Silakan masuk menggunakan akun Anda untuk mengelola website.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-8">
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}"
                          @submit="if (!$event.target.email.value.includes('@')) { showEmailAlert = true; $event.preventDefault(); }">
                        @csrf

                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </span>
                                <input id="email" type="text" name="email" x-ref="emailInput" :value="old('email')" required autofocus autocomplete="username"
                                       @input="if ($event.target.value.includes('@')) showEmailAlert = false"
                                       class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-600/20"
                                       placeholder="nama@email.com">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Password</label>
                            <div class="relative" x-data="{ show: false }">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                </span>
                                <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                       class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 pl-11 pr-12 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-600/20"
                                       placeholder="••••••••">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-emerald-700" aria-label="Tampilkan Password">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3-8a10 10 0 00-9.677 7.357A.996.996 0 002.5 12c.153.443.237.91.237 1.643V12a10 10 0 0019.5 0 .996.996 0 00-.176-.643A10 10 0 0012 4z" /></svg>
                                    <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="mt-5 flex items-center">
                            <label class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" name="remember"
                                       class="h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600/30">
                                <span class="ml-2 text-sm text-slate-600">Ingat Saya</span>
                            </label>
                        </div>

                        <button type="submit"
                                class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-700/30 transition hover:bg-emerald-800">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                            Masuk
                        </button>
                    </form>

                    {{-- Peringatan: format email tidak valid --}}
            <div x-show="showEmailAlert"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="mt-4 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3"
                 role="alert">
                <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-500 text-white shadow-sm shadow-amber-500/40">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM12 3v.008h.008V4.5m0 .008a8.992 8.992 0 100 17.984 8.992 8.992 0 000-17.984z"/>
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-amber-900">Format Email Tidak Valid</p>
                    <p class="mt-0.5 text-sm leading-snug text-amber-800">Pastikan Anda memasukkan alamat email yang lengkap.</p>
                </div>
                <button type="button" @click="showEmailAlert = false; $refs.emailInput.focus()" aria-label="Tutup" class="shrink-0 rounded-md p-1 text-amber-400 transition hover:bg-amber-100 hover:text-amber-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            @php
                $waNumber = preg_replace('/\D+/', '', $settings['whatsapp'] ?? '');
            @endphp

            @if ($waNumber)
                <div class="mt-5 flex items-center justify-center gap-3">
                    <span class="h-px flex-1 bg-slate-200"></span>
                    <span class="text-xs font-medium text-slate-400">Lupa Password?</span>
                    <span class="h-px flex-1 bg-slate-200"></span>
                </div>
                <a href="{{ 'https://wa.me/'.$waNumber }}?text=Halo {{ $settings['site_name'] ?? 'admin' }}, saya lupa password akun admin website. Mohon bantuannya." target="_blank" rel="noopener"
                   class="mt-4 flex items-center justify-center gap-2 rounded-xl border border-emerald-600/30 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 transition hover:bg-emerald-100">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Chat Admin via WhatsApp
                </a>
            @endif

            <a href="{{ route('home') }}" class="mt-4 flex items-center justify-center gap-1.5 text-sm font-semibold text-slate-500 transition hover:text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
