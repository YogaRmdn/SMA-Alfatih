<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'Super Admin', 'slug' => 'super_admin']);
        Role::create(['name' => 'Admin', 'slug' => 'admin']);
    }

    public function test_it_creates_admin_accounts_with_the_password_from_env(): void
    {
        config(['app.env' => 'local']);
        putenv('SEED_ADMIN_PASSWORD=RahasiaKuat123');
        $_ENV['SEED_ADMIN_PASSWORD'] = 'RahasiaKuat123';

        try {
            $this->seed(DatabaseSeeder::class);
        } finally {
            putenv('SEED_ADMIN_PASSWORD');
            unset($_ENV['SEED_ADMIN_PASSWORD']);
        }

        $superAdmin = User::where('email', 'superadmin@alfatih.sch.id')->first();
        $admin = User::where('email', 'admin@alfatih.sch.id')->first();

        $this->assertNotNull($superAdmin);
        $this->assertNotNull($admin);
        $this->assertTrue(Hash::check('RahasiaKuat123', $superAdmin->password));
        $this->assertTrue(Hash::check('RahasiaKuat123', $admin->password));

        $this->assertSame('super_admin', $superAdmin->role->slug);
        $this->assertSame('admin', $admin->role->slug);
    }

    public function test_it_never_falls_back_to_a_known_default_password(): void
    {
        config(['app.env' => 'local']);
        putenv('SEED_ADMIN_PASSWORD');
        unset($_ENV['SEED_ADMIN_PASSWORD']);

        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::where('email', 'superadmin@alfatih.sch.id')->first();
        $admin = User::where('email', 'admin@alfatih.sch.id')->first();

        $this->assertNotNull($superAdmin);
        $this->assertNotNull($admin);

        $this->assertFalse(Hash::check('password', $superAdmin->password));
        $this->assertFalse(Hash::check('password', $admin->password));
    }

    public function test_reseeding_does_not_reset_an_existing_admin_password(): void
    {
        config(['app.env' => 'local']);
        putenv('SEED_ADMIN_PASSWORD');
        unset($_ENV['SEED_ADMIN_PASSWORD']);

        $existing = new User;
        $existing->name = 'Super Admin';
        $existing->email = 'superadmin@alfatih.sch.id';
        $existing->password = Hash::make('PasswordYangSudahDiubah');
        $existing->email_verified_at = now();
        $existing->save();

        $this->seed(DatabaseSeeder::class);

        $this->assertTrue(
            Hash::check('PasswordYangSudahDiubah', $existing->refresh()->password),
            'db:seed menimpa password admin yang sudah ada'
        );
    }

    public function test_it_refuses_to_seed_admin_accounts_in_production_without_env_password(): void
    {
        // app()->environment() membaca binding 'env' di container, bukan config().
        app()->instance('env', 'production');
        putenv('SEED_ADMIN_PASSWORD');
        unset($_ENV['SEED_ADMIN_PASSWORD']);

        $this->expectException(RuntimeException::class);

        // Dipanggil langsung (bukan lewat db:seed) supaya tidak diganggu
        // prompt konfirmasi "Do you really wish to run this command?".
        (new DatabaseSeeder)->run();
    }
}
