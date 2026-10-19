<x-sibel.layouts.auth title="Masuk">
    <div class="mb-6 text-center">
        <p class="text-xs font-bold uppercase tracking-widest text-emerald-600">SIBEL &middot; SMA Alfatih</p>
        <h1 class="mt-1 text-xl font-extrabold text-slate-800">Masuk ke SIBEL</h1>
        <p class="mt-1.5 text-sm text-slate-500">
            Masuk untuk mengisi kuesioner gaya belajar dan melihat hasil asesmenmu.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('sibel.login.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-bold text-slate-700">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="nama@email.com"
                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-bold text-slate-700">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center">
            <label class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                       class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500/30">
                <span class="ml-2 text-sm text-slate-600">Ingat saya</span>
            </label>
        </div>

        <button
            type="submit"
            class="w-full rounded-lg bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600 px-4 py-3 text-sm font-extrabold uppercase tracking-wide text-white shadow-md shadow-emerald-500/30 transition hover:brightness-105"
        >
            Masuk
        </button>
    </form>

    <div class="mt-5 flex flex-col items-center gap-1.5 text-center text-sm text-slate-500">
        <p>
            Belum punya akun?
            <a href="{{ route('sibel.register') }}" class="font-bold text-emerald-700 transition hover:text-emerald-900">Daftar</a>
        </p>
        <p class="text-xs text-slate-400">Lupa password? Hubungi guru BK sekolah Anda.</p>
    </div>
</x-sibel.layouts.auth>
