<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\SibelAnswer;
use App\Models\SibelAttempt;
use App\Models\SibelClass;
use App\Models\SibelQuestionnaire;
use App\Models\SibelStudent;
use App\Models\User;
use App\Services\LearningStyleScoringService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Data contoh SIBEL untuk pengembangan lokal.
 *
 * TIDAK dipanggil otomatis oleh DatabaseSeeder (sama seperti DemoSeeder).
 * Jalankan manual: php artisan db:seed --class=SibelDemoSeeder
 *
 * Password akun login siswa contoh dibuat acak dan dicetak ke terminal —
 * tidak ada kredensial hardcoded di repo.
 */
class SibelDemoSeeder extends Seeder
{
    /**
     * Pola jawaban per siswa (tepat 15 karakter untuk 15 pertanyaan):
     * A=Visual, B=Auditori, C=Kinestetik. Beberapa pola sengaja menghasilkan
     * seri untuk menguji tampilan hasil seri.
     */
    protected const PATTERNS = [
        'AAABBBCCCABCABC', // A6 B6 C3  -> seri Visual & Auditori
        'AAAABBBBCCABCCA', // A6 B5 C4  -> Visual
        'AAAAAABBBBBBCCC', // A6 B6 C3  -> seri Visual & Auditori
        'CCCCCCCCCCCCCCC', // A0 B0 C15 -> Kinestetik
        'BBBBBBBBBBBBBBB', // A0 B15 C0 -> Auditori
        'AAACCCBBBAAACCC', // A6 B3 C6  -> seri Visual & Kinestetik
        'ABCCABCCABCCABC', // A4 B4 C7  -> Kinestetik
        'CCCCCCCCCCBBBBB', // A0 B5 C10 -> Kinestetik
    ];

    public function run(): void
    {
        $questionnaire = SibelQuestionnaire::where('status', SibelQuestionnaire::STATUS_ACTIVE)
            ->orderByDesc('id')
            ->first()
            ?? SibelQuestionnaire::firstOrCreate(
                ['title' => 'Kuesioner Kecenderungan Gaya Belajar Siswa'],
                ['version' => '1.0', 'status' => SibelQuestionnaire::STATUS_ACTIVE]
            );

        if (! $questionnaire->isActive()) {
            $questionnaire->update(['status' => SibelQuestionnaire::STATUS_ACTIVE]);
        }

        if (! $questionnaire->questions()->exists()) {
            $this->call(SibelSeeder::class);
            $questionnaire->refresh();
        }

        $classes = collect([
            ['name' => 'X-A', 'academic_year' => '2026/2027'],
            ['name' => 'X-B', 'academic_year' => '2026/2027'],
            ['name' => 'XI-IPA-1', 'academic_year' => '2026/2027'],
        ])->map(fn (array $class) => SibelClass::updateOrCreate(
            ['name' => $class['name'], 'academic_year' => $class['academic_year']],
            $class
        ));

        $names = [
            'Aisyah Rahmawati',
            'Bima Saputra',
            'Citra Lestari',
            'Daffa Pratama',
            'Elina Maharani',
            'Fajar Nugroho',
            'Ghina Salsabila',
            'Hafiz Ramadhan',
            'Intan Permata',
        ];

        $students = collect($names)->map(fn (string $name, int $index) => SibelStudent::firstOrCreate(
            ['full_name' => $name],
            [
                'class_id' => $classes[$index % $classes->count()]->id,
                'student_number' => 'SBL-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'status' => SibelStudent::STATUS_ACTIVE,
            ]
        ));

        // Dua akun login siswa contoh (password acak dicetak).
        $this->createDemoLogin($students[0]);
        $this->createDemoLogin($students[1]);

        $service = app(LearningStyleScoringService::class);
        $patterns = array_values(self::PATTERNS);

        foreach ($students as $index => $student) {
            if ($student->attempts()->exists()) {
                continue;
            }

            // Siswa terakhir menyisakan satu sesi yang masih berjalan.
            if ($index === $students->count() - 1) {
                SibelAttempt::create([
                    'student_id' => $student->id,
                    'questionnaire_id' => $questionnaire->id,
                    'status' => SibelAttempt::STATUS_IN_PROGRESS,
                    'started_at' => now()->subHours(2),
                ]);

                continue;
            }

            $attempt = SibelAttempt::create([
                'student_id' => $student->id,
                'questionnaire_id' => $questionnaire->id,
                'status' => SibelAttempt::STATUS_COMPLETED,
                'started_at' => now()->subDays(7 - $index),
                'completed_at' => now()->subDays(7 - $index)->addMinutes(25),
            ]);

            $pattern = $patterns[$index % count($patterns)];

            foreach ($questionnaire->questions->values() as $qIndex => $question) {
                $code = strtoupper($pattern[$qIndex] ?? 'A');
                $option = $question->options->firstWhere('option_code', $code)
                    ?? $question->options->first();

                SibelAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'option_id' => $option->id,
                ]);
            }

            $result = $service->scoreAndPersist($attempt);

            // Enam hasil pertama sudah dipublikasikan; dua lainnya menunggu.
            if ($index < 6) {
                $result->update(['is_published' => true]);
            }
        }

        $this->command?->info('Data demo SIBEL siap: 3 kelas, '.$students->count().' siswa, hasil kuesioner contoh.');
    }

    protected function createDemoLogin(SibelStudent $student): void
    {
        if ($student->user_id) {
            return;
        }

        $email = 'siswa.'.Str::slug(strtolower($student->full_name)).'@demo.local';

        $existing = User::where('email', $email)->first();

        if ($existing) {
            $student->update(['user_id' => $existing->id]);

            return;
        }

        $role = Role::firstOrCreate(
            ['slug' => 'siswa'],
            ['name' => 'Siswa', 'description' => 'Mengisi kuesioner gaya belajar dan melihat hasil miliknya sendiri.']
        );

        $password = Str::password(16);

        $user = new User;
        $user->name = $student->full_name;
        $user->email = $email;
        $user->password = $password;
        $user->email_verified_at = now();
        $user->role_id = $role->id;
        $user->save();

        $student->update(['user_id' => $user->id]);

        $this->command?->warn("Akun login siswa contoh: {$email} | Password: {$password}");
    }
}
