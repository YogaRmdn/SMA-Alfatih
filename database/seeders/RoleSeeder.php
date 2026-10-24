<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Memiliki akses penuh ke seluruh fitur, termasuk kelola user.',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Mengelola seluruh konten website.',
            ],
            [
                'name' => 'Guru BK',
                'slug' => 'guru_bk',
                'description' => 'Pengelola modul SIBEL — data siswa, kuesioner gaya belajar, hasil, dan rekap kelas. TIDAK berhak atas modul TOEFL.',
            ],
            [
                'name' => 'Admin SIBEL',
                'slug' => 'admin_sibel',
                'description' => 'Admin khusus modul SIBEL — hanya bisa mengelola SIBEL, tanpa akses panel website & TOEFL.',
            ],
            [
                'name' => 'Admin TOEFL',
                'slug' => 'admin_toefl',
                'description' => 'Admin khusus modul TOEFL — ujian, soal, kode akses, peserta, dan hasil. TIDAK berhak atas modul SIBEL maupun panel website.',
            ],
            [
                'name' => 'Siswa',
                'slug' => 'siswa',
                'description' => 'Mengisi kuesioner gaya belajar dan melihat hasil miliknya sendiri.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
