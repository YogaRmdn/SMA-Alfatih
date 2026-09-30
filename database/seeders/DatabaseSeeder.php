<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Akun awal yang dibuat kalau email-nya belum terdaftar.
     *
     * Password TIDAK pernah ditulis ulang untuk akun yang sudah ada, supaya
     * `db:seed` tidak diam-diam mengembalikan password admin ke nilai default
     * yang diketahui publik (risiko account takeover).
     *
     * Password awal diambil dari env SEED_ADMIN_PASSWORD. Kalau env itu kosong,
     * di produksi proses ini dibatalkan — akun admin tidak boleh dibuat dengan
     * password tebakan. Di environment lain (local/testing) password acak
     * dicetak ke terminal supaya tetap bisa login tanpa kode hardcoded di repo.
     */
    protected const ACCOUNTS = [
        [
            'email' => 'superadmin@alfatih.sch.id',
            'name' => 'Super Admin',
            'role' => 'super_admin',
        ],
        [
            'email' => 'admin@alfatih.sch.id',
            'name' => 'Admin Sekolah',
            'role' => 'admin',
        ],
    ];

    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SettingsSeeder::class,
            PpdbFormFieldSeeder::class,
        ]);

        $secret = $this->initialPassword();

        foreach (self::ACCOUNTS as $account) {
            $this->createAccount($account, $secret);
        }
    }

    /**
     * @param  array{email: string, name: string, role: string}  $account
     */
    protected function createAccount(array $account, string $secret): void
    {
        $existing = User::query()->where('email', $account['email'])->first();

        if ($existing) {
            $this->ensureRole($existing, $account['role']);

            $this->command?->warn(sprintf(
                'Akun %s sudah ada — password tidak diubah.',
                $account['email']
            ));

            return;
        }

        $user = new User;
        $user->name = $account['name'];
        $user->email = $account['email'];
        $user->password = Hash::make($secret);
        $user->email_verified_at = now();
        $user->save();

        $this->ensureRole($user, $account['role']);

        $this->command?->info(sprintf(
            'Akun %s dibuat. Password awal: %s',
            $account['email'],
            $secret
        ));
    }

    protected function ensureRole(User $user, string $roleSlug): void
    {
        $roleId = Role::query()->where('slug', $roleSlug)->value('id');

        if ($roleId && $user->role_id !== $roleId) {
            $user->role_id = $roleId;
            $user->save();
        }
    }

    protected function initialPassword(): string
    {
        $configured = (string) env('SEED_ADMIN_PASSWORD', '');

        if ($configured !== '') {
            return $configured;
        }

        if (app()->environment('production')) {
            throw new \RuntimeException(
                'SEED_ADMIN_PASSWORD wajib diisi di environment production. '
                .'Isi di .env, atau buat akun admin lewat panel lalu jalankan ulang tanpa seeder akun.'
            );
        }

        $generated = Str::password(24);

        $this->command?->warn(
            'SEED_ADMIN_PASSWORD tidak disetel — memakai password acak. Catat password di atas.'
        );

        return $generated;
    }
}
