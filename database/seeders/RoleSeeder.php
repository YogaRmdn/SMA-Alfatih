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
                'description' => 'Mengelola data siswa, kuesioner SIBEL, hasil, dan rekap kelas.',
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
