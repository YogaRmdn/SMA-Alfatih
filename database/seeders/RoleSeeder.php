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
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
