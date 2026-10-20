<x-sibel.layouts.auth title="Pilih Identitas Siswa">
    <div class="text-center">
        <h1 class="text-lg font-bold text-slate-800">Selamat Datang di SIBEL 👋</h1>
        <p class="mt-1 text-sm text-slate-500">
            Pilih identitas Anda untuk mulai mengisi kuesioner gaya belajar.
        </p>
    </div>

    @if ($errors->any())
        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm font-medium text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('sibel.identify') }}" class="mt-5 space-y-4">
        @csrf

        <div>
            <label for="kelas" class="mb-1.5 block text-sm font-semibold text-slate-700">Kelas <span class="text-red-500">*</span></label>
            <select name="kelas" id="kelas" onchange="this.form.submit()"
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">Semua kelas</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" @selected((int) request('kelas') === $class->id)>
                        {{ $class->label() }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-400">Memilih kelas menyaring daftar nama di bawah.</p>
        </div>

        <div>
            <label for="student_id" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama Saya <span class="text-red-500">*</span></label>
            <select name="student_id" id="student_id" required
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <option value="">— Pilih nama Anda —</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" @selected((int) old('student_id') === $student->id)>
                        {{ $student->full_name }}{{ $student->student_number ? ' — '.$student->student_number : '' }}{{ $student->schoolClass ? ' ('.$student->schoolClass->name.')' : '' }}
                    </option>
                @endforeach
            </select>
            @if ($students->isEmpty())
                <p class="mt-1 text-xs font-medium text-amber-600">Belum ada siswa aktif pada kelas ini. Silakan hubungi guru BK.</p>
            @endif
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-emerald-800">
            Mulai Mengisi Kuesioner
        </button>
    </form>

    <p class="mt-4 rounded-xl bg-sky-50 p-3 text-xs leading-relaxed text-sky-800">
        <strong>Catatan:</strong> pastikan Anda memilih nama yang benar — seluruh jawaban dan hasil kuesioner
        akan tercatat atas nama tersebut. Bila nama Anda tidak ada dalam daftar, hubungi guru BK.
    </p>
</x-sibel.layouts.auth>
