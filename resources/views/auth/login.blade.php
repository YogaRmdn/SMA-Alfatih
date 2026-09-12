<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/Logo SMK fix 4.png') }}">
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
                    <img src="{{ asset('img/Logo SMK fix 4.png') }}" alt="Logo" class="h-12 w-12 rounded-xl bg-white object-contain p-1">
                    <span class="text-sm font-bold text-white">{{ $siteName }}</span>
                </div>

                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-400 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-emerald-950">
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
        <div class="flex w-full items-center justify-center bg-slate-50 px-4 py-10 lg:w-1/2 lg:px-10 xl:w-2/5">
            <div class="w-full max-w-md">

                <div class="mb-8 flex flex-col items-center text-center lg:items-start lg:text-left">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 lg:hidden">
                        <img src="{{ asset('img/Logo SMK fix 4.png') }}" alt="Logo" class="h-12 w-12 rounded-xl bg-white object-contain p-1 shadow-sm">
                        <span class="text-sm font-bold leading-tight text-emerald-950">{{ $siteName }}</span>
                    </a>
                    <span class="mt-6 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-700 text-white shadow-lg shadow-emerald-700/30">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </span>
                    <h2 class="mt-4 text-2xl font-extrabold text-emerald-950">Masuk ke Panel Admin</h2>
                    <p class="mt-1.5 text-sm text-slate-500">Silakan masuk menggunakan akun Anda untuk mengelola website.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-8">
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </span>
                                <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                                       class="w-full rounded-xl border border-slate-300 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-600/20"
                                       placeholder="admin@alfatih.sch.id">
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
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-emerald-700" aria-label="Tampilkan password">
                                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3-8a10 10 0 00-9.677 7.357A.996.996 0 002.5 12c.153.443.237.91.237 1.643V12a10 10 0 0019.5 0 .996.996 0 00-.176-.643A10 10 0 0012 4z" /></svg>
                                    <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="mt-5 flex items-center justify-between">
                            <label class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" name="remember"
                                       class="h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600/30">
                                <span class="ml-2 text-sm text-slate-600">Ingat saya</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Lupa password?</a>
                            @endif
                        </div>

                        <button type="submit"
                                class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-700/30 transition hover:bg-emerald-800">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                            Masuk
                        </button>
                    </form>

                    <a href="{{ route('home') }}" class="mt-4 flex items-center justify-center gap-1.5 text-sm font-semibold text-slate-500 transition hover:text-emerald-700">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali ke Beranda
                    </a>
                </div>

                {{-- Akun demo --}}
                <div class="mt-6 overflow-hidden rounded-2xl border border-amber-300 bg-amber-50">
                    <div class="flex items-center gap-2 border-b border-amber-200 bg-amber-100 px-4 py-3">
                        <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        <span class="text-xs font-bold uppercase tracking-wide text-amber-800">Akun Demo — Khusus Administrator</span>
                    </div>
                    <div class="divide-y divide-amber-200/70 px-4">
                        @php
                            $dummyAccounts = [
                                ['role' => 'Super Admin', 'email' => 'superadmin@alfatih.sch.id', 'badge' => 'Akses Penuh', 'color' => 'bg-emerald-700'],
                                ['role' => 'Admin Sekolah', 'email' => 'admin@alfatih.sch.id', 'badge' => 'Kelola Konten', 'color' => 'bg-amber-500'],
                            ];
                        @endphp
                        @foreach ($dummyAccounts as $account)
                            <div class="flex items-center justify-between gap-3 py-3.5">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-lg {{ $account['color'] }} text-xs font-bold text-white">
                                        {{ strtoupper(substr($account['role'], 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $account['role'] }}</p>
                                        <p class="text-xs text-slate-500">{{ $account['email'] }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="rounded-md bg-white px-2 py-0.5 text-[11px] font-semibold text-slate-600 ring-1 ring-slate-200">password</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t border-amber-200 bg-amber-100/60 px-4 py-2.5 text-center text-[11px] text-amber-700">
                        Password semua akun demo: <span class="font-mono font-bold">password</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
