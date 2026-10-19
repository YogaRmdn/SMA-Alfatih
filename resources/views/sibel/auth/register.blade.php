<x-sibel.layouts.auth title="Daftar">
    <div class="mb-6 text-center">
        <p class="text-xs font-bold uppercase tracking-widest text-emerald-600">SIBEL &middot; SMA Alfatih</p>
        <h1 class="mt-1 text-xl font-extrabold text-slate-800">Daftar Akun Siswa</h1>
        <p class="mt-1.5 text-sm text-slate-500">
            Setelah mendaftar, kamu akan langsung masuk dan diarahkan ke kuesioner gaya belajar.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('sibel.register.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="full_name" class="mb-1.5 block text-sm font-bold text-slate-700">Nama Lengkap</label>
            <input
                id="full_name"
                type="text"
                name="full_name"
                value="{{ old('full_name') }}"
                required
                autofocus
                placeholder="Nama sesuai identitas"
                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
            >
            @error('full_name')
                <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @if ($classes->isNotEmpty())
            <div>
                <label for="class_id" class="mb-1.5 block text-sm font-bold text-slate-700">Kelas <span class="font-medium text-slate-400">(opsional)</span></label>
                <select
                    id="class_id"
                    name="class_id"
                    class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                >
                    <option value="">— Belum tahu / kosongkan saja —</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected((string) old('class_id') === (string) $class->id)>
                            {{ $class->name }}@if ($class->academic_year) &middot; {{ $class->academic_year }}@endif
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-400">Bisa dikosongkan — guru BK bisa mengatur kelasnya nanti.</p>
                @error('class_id')
                    <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div>
            <label for="student_number" class="mb-1.5 block text-sm font-bold text-slate-700">Nomor Induk / NIS <span class="font-medium text-slate-400">(opsional)</span></label>
            <input
                id="student_number"
                type="text"
                name="student_number"
                value="{{ old('student_number') }}"
                maxlength="30"
                placeholder="Kosongkan jika tidak tahu"
                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
            >
            @error('student_number')
                <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-sm font-bold text-slate-700">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                placeholder="nama@email.com"
                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
            >
            @error('email')
                <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-bold text-slate-700">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                placeholder="Minimal 8 karakter"
                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
            >
            @error('password')
                <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-bold text-slate-700">Ulangi Password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                class="block w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
            >
        </div>

        <button
            type="submit"
            class="w-full rounded-lg bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600 px-4 py-3 text-sm font-extrabold uppercase tracking-wide text-white shadow-md shadow-emerald-500/30 transition hover:brightness-105"
        >
            Daftar &amp; Mulai Mengisi
        </button>
    </form>

    <div class="mt-5 flex flex-col items-center gap-1.5 text-center text-sm text-slate-500">
        <p>
            Sudah punya akun?
            <a href="{{ route('sibel.login') }}" class="font-bold text-emerald-700 transition hover:text-emerald-900">Masuk</a>
        </p>
        <a href="/" class="text-xs text-slate-400 transition hover:text-slate-600">&larr; Kembali ke beranda</a>
    </div>
</x-sibel.layouts.auth>
