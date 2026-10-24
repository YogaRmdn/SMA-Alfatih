<?php

namespace Tests\Concerns;

use App\Models\Role;
use App\Models\SibelAnswer;
use App\Models\SibelAttempt;
use App\Models\SibelClass;
use App\Models\SibelOption;
use App\Models\SibelQuestion;
use App\Models\SibelQuestionnaire;
use App\Models\SibelStudent;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * Pembangun data SIBEL untuk Feature/Unit test.
 */
trait BuildsSibelData
{
    protected function createSibelRoles(): void
    {
        collect([
            ['name' => 'Super Admin', 'slug' => 'super_admin'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Guru BK', 'slug' => 'guru_bk'],
            ['name' => 'Admin SIBEL', 'slug' => 'admin_sibel'],
            ['name' => 'Siswa', 'slug' => 'siswa'],
        ])->each(fn (array $role) => Role::create($role));
    }

    /**
     * Buat kuesioner aktif dengan N pertanyaan wajib, tiap pertanyaan punya
     * opsi A (Visual), B (Auditori), C (Kinestetik).
     */
    protected function createQuestionnaire(
        int $questionCount,
        string $status = SibelQuestionnaire::STATUS_ACTIVE
    ): SibelQuestionnaire {
        $questionnaire = SibelQuestionnaire::create([
            'title' => 'Kuesioner Gaya Belajar',
            'version' => '1.0',
            'status' => $status,
        ]);

        for ($i = 1; $i <= $questionCount; $i++) {
            $question = SibelQuestion::create([
                'questionnaire_id' => $questionnaire->id,
                'question_text' => "Pertanyaan ke-{$i}",
                'order_number' => $i,
                'is_required' => true,
            ]);

            foreach (config('sibel.code_category') as $code => $category) {
                SibelOption::create([
                    'question_id' => $question->id,
                    'option_code' => $code,
                    'option_text' => "Pilihan {$code} untuk pertanyaan {$i}",
                    'category_code' => $category,
                ]);
            }
        }

        return $questionnaire;
    }

    /**
     * Buat kuesioner Skala Likert aktif: N pernyataan (kategori indikator
     * berurutan V-A-K) dengan empat opsi skala (nilai 4-1) per pernyataan.
     */
    protected function createLikertQuestionnaire(
        int $questionCount = 6,
        string $status = SibelQuestionnaire::STATUS_ACTIVE
    ): SibelQuestionnaire {
        $questionnaire = SibelQuestionnaire::create([
            'title' => 'Kuesioner Likert Gaya Belajar',
            'version' => '2.0',
            'scoring_method' => SibelQuestionnaire::SCORING_METHOD_LIKERT,
            'status' => $status,
        ]);

        $categories = array_keys(config('sibel.categories'));

        for ($i = 1; $i <= $questionCount; $i++) {
            $question = SibelQuestion::create([
                'questionnaire_id' => $questionnaire->id,
                'question_text' => "Pernyataan ke-{$i}",
                'category_code' => $categories[($i - 1) % count($categories)],
                'order_number' => $i,
                'is_required' => true,
            ]);

            foreach (config('sibel.likert.levels') as $level) {
                SibelOption::create([
                    'question_id' => $question->id,
                    'option_code' => $level['code'],
                    'option_text' => $level['label'],
                    'value' => $level['value'],
                ]);
            }
        }

        return $questionnaire;
    }

    protected function createSibelUser(string $roleSlug, ?string $name = null): User
    {
        $user = new User;
        $user->name = $name ?? 'User '.ucfirst($roleSlug);
        $user->email = $roleSlug.'.'.Str::random(8).'@test.local';
        $user->password = 'password';
        $user->email_verified_at = now();
        $user->role_id = Role::where('slug', $roleSlug)->first()->id;
        $user->save();

        return $user;
    }

    protected function createStudentFor(User $user, ?SibelClass $class = null): SibelStudent
    {
        $class ??= SibelClass::create(['name' => 'X-A', 'academic_year' => '2026/2027']);

        return SibelStudent::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'full_name' => $user->name,
            'status' => SibelStudent::STATUS_ACTIVE,
        ]);
    }

    /**
     * Siswa murni identitas (tanpa akun login) — dipilih lewat portal /sibel.
     */
    protected function createStandaloneStudent(string $name, ?SibelClass $class = null): SibelStudent
    {
        $class ??= SibelClass::create(['name' => 'X-A', 'academic_year' => '2026/2027']);

        return SibelStudent::create([
            'class_id' => $class->id,
            'full_name' => $name,
            'status' => SibelStudent::STATUS_ACTIVE,
        ]);
    }

    /**
     * Ikat identitas siswa ke sesi browser (cara siswa masuk tanpa login).
     */
    protected function actingAsStudent(SibelStudent $student): static
    {
        return $this->withSession(['sibel_student_id' => $student->id]);
    }

    /**
     * Buat sesi pengisian lengkap dengan jawaban sesuai pola kode
     * (mis. "AAABBBCCC" — satu karakter per pertanyaan, berurutan).
     */
    protected function createAttempt(
        SibelStudent $student,
        SibelQuestionnaire $questionnaire,
        ?string $pattern = null,
        string $status = SibelAttempt::STATUS_COMPLETED
    ): SibelAttempt {
        $attempt = SibelAttempt::create([
            'student_id' => $student->id,
            'questionnaire_id' => $questionnaire->id,
            'status' => $status,
            'started_at' => now()->subHour(),
            'completed_at' => $status === SibelAttempt::STATUS_COMPLETED ? now() : null,
        ]);

        if ($pattern !== null) {
            $this->fillAnswers($attempt, $pattern);
        }

        return $attempt;
    }

    protected function fillAnswers(SibelAttempt $attempt, string $pattern): void
    {
        $questions = $attempt->questionnaire->questions()->get();

        foreach ($questions as $index => $question) {
            // Karakter pattern menentukan pertanyaan mana yang dijawab —
            // sisanya sengaja dibiarkan kosong untuk uji ketidaklengkapan.
            if (! isset($pattern[$index])) {
                continue;
            }

            $code = strtoupper($pattern[$index]);

            $option = $question->options()->where('option_code', $code)->firstOrFail();

            SibelAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'option_id' => $option->id,
            ]);
        }
    }
}
