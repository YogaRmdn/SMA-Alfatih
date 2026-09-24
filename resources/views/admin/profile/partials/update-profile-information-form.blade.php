<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <x-admin.image-upload name="avatar" label="Foto Profil" :path="$user->avatar" fit="300x300" hint="Format JPG/PNG/WebP, maks. 256 MB. Foto akan ditampilkan di pojok kanan atas panel admin." />

        <x-admin.field label="Nama Lengkap" name="name" required>
            <x-admin.input name="name" :value="old('name', $user->name)" required autocomplete="name" autofocus placeholder="Nama lengkap Anda" />
        </x-admin.field>

        <x-admin.field label="Alamat Email" name="email" required>
            <x-admin.input name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" placeholder="nama@contoh.com" />
        </x-admin.field>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                <p class="font-medium">Alamat email Anda belum diverifikasi.</p>
                <p class="mt-1">
                    Klik tombol di bawah untuk mengirim ulang email verifikasi.
                    <button form="send-verification" class="font-semibold underline decoration-amber-400 underline-offset-2 transition hover:text-amber-900">
                        Kirim ulang email verifikasi
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-semibold text-emerald-700">
                        Tautan verifikasi baru telah dikirim ke email Anda.
                    </p>
                @endif
            </div>
        @endif

        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
               class="rounded-lg bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700">
                Profil berhasil disimpan.
            </p>
        @endif

        <div class="flex items-center justify-end">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-emerald-600/30 transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                Simpan Profil
            </button>
        </div>
    </form>
</section>