<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SettingsSeeder::class,
        ]);

        $superAdminRoleId = Role::where('slug', 'super_admin')->value('id');
        $adminRoleId = Role::where('slug', 'admin')->value('id');

        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@alfatih.sch.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->role_id = $superAdminRoleId;
        $superAdmin->save();

        $admin = User::updateOrCreate(
            ['email' => 'admin@alfatih.sch.id'],
            [
                'name' => 'Admin Sekolah',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->role_id = $adminRoleId;
        $admin->save();
    }
}
