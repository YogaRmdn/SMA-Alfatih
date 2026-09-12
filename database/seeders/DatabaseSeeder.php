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

        $superAdmin = Role::where('slug', 'super_admin')->first();
        $admin = Role::where('slug', 'admin')->first();

        User::updateOrCreate(
            ['email' => 'superadmin@alfatih.sch.id'],
            [
                'role_id' => $superAdmin->id,
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@alfatih.sch.id'],
            [
                'role_id' => $admin->id,
                'name' => 'Admin Sekolah',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
