<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Setting;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherGenderOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'Super Admin', 'slug' => 'super_admin']);
        Role::create(['name' => 'Admin', 'slug' => 'admin']);
        Setting::set('site_name', 'SMA IT Tahfizh Al-Fatih');
    }

    public function test_public_home_shows_male_teachers_before_female_teachers(): void
    {
        Teacher::create(['name' => 'Zulaikha P', 'gender' => Teacher::GENDER_P, 'sort_order' => 1, 'is_active' => true]);
        Teacher::create(['name' => 'Ahmad L', 'gender' => Teacher::GENDER_L, 'sort_order' => 2, 'is_active' => true]);
        Teacher::create(['name' => 'Budi L', 'gender' => Teacher::GENDER_L, 'sort_order' => 1, 'is_active' => true]);
        Teacher::create(['name' => 'Siti P', 'gender' => Teacher::GENDER_P, 'sort_order' => 2, 'is_active' => true]);

        $names = Teacher::query()->where('is_active', true)->orderedByGender()->pluck('name')->all();

        $this->assertSame(
            ['Budi L', 'Ahmad L', 'Zulaikha P', 'Siti P'],
            $names,
            'Laki-laki harus muncul lebih dulu, lalu perempuan, masing-masing urut sort_order.'
        );
    }

    public function test_teachers_without_gender_are_listed_last(): void
    {
        $legacy = Teacher::create(['name' => 'Guru Lama', 'is_active' => true]);
        Teacher::create(['name' => 'Siti P', 'gender' => Teacher::GENDER_P, 'is_active' => true]);
        Teacher::create(['name' => 'Ahmad L', 'gender' => Teacher::GENDER_L, 'is_active' => true]);

        $this->assertNull($legacy->gender);

        $names = Teacher::query()->orderedByGender()->pluck('name')->all();

        $this->assertSame(['Ahmad L', 'Siti P', 'Guru Lama'], $names);
    }

    public function test_admin_index_lists_teachers_grouped_by_gender(): void
    {
        Teacher::create(['name' => 'Guru Legacy', 'is_active' => true]);
        Teacher::create(['name' => 'Budi L', 'gender' => Teacher::GENDER_L, 'is_active' => true]);
        Teacher::create(['name' => 'Siti P', 'gender' => Teacher::GENDER_P, 'is_active' => true]);

        $this->actingAs($this->admin())
            ->get(route('admin.teachers.index'))
            ->assertOk()
            ->assertSeeInOrder(['Budi L', 'Siti P', 'Guru Legacy']);
    }

    public function test_store_requires_gender(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.teachers.store'), ['name' => 'Tanpa Gender'])
            ->assertSessionHasErrors('gender');

        $this->assertDatabaseMissing('teachers', ['name' => 'Tanpa Gender']);
    }

    public function test_store_rejects_unknown_gender(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.teachers.store'), ['name' => 'Gender Ngawur', 'gender' => 'X'])
            ->assertSessionHasErrors('gender');

        $this->assertDatabaseMissing('teachers', ['name' => 'Gender Ngawur']);
    }

    public function test_admin_can_save_new_teacher_with_gender(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.teachers.store'), ['name' => 'Siti Aminah', 'gender' => Teacher::GENDER_P])
            ->assertRedirect(route('admin.teachers.index'));

        $this->assertDatabaseHas('teachers', ['name' => 'Siti Aminah', 'gender' => Teacher::GENDER_P]);

        $teacher = Teacher::where('name', 'Siti Aminah')->firstOrFail();

        $this->assertSame('Perempuan', $teacher->gender_label);
    }

    public function test_gender_label_is_null_when_not_set(): void
    {
        $teacher = Teacher::create(['name' => 'Guru Tanpa Gender']);

        $this->assertNull($teacher->gender_label);
    }

    public function test_update_changes_gender(): void
    {
        $teacher = Teacher::create(['name' => 'Ahmad L', 'gender' => Teacher::GENDER_L]);

        $this->actingAs($this->admin())
            ->put(route('admin.teachers.update', $teacher), [
                'name' => 'Ahmad L',
                'gender' => Teacher::GENDER_P,
            ])
            ->assertRedirect(route('admin.teachers.index'));

        $this->assertSame(Teacher::GENDER_P, $teacher->fresh()->gender);
    }

    public function test_teacher_form_offers_both_gender_options(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.teachers.create'))
            ->assertOk()
            ->assertSee('Laki-laki')
            ->assertSee('Perempuan');
    }

    private function admin(): User
    {
        return User::factory()->create([
            'role_id' => Role::where('slug', 'admin')->value('id'),
            'email_verified_at' => now(),
        ]);
    }
}
