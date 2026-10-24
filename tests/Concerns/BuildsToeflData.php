<?php

namespace Tests\Concerns;

use App\Models\Role;
use App\Models\ToeflAttempt;
use App\Models\ToeflAudioAsset;
use App\Models\ToeflExam;
use App\Models\ToeflQuestion;
use App\Models\ToeflSection;
use App\Models\ToeflStudent;
use App\Services\ToeflAccessCodeService;
use Illuminate\Support\Collection;

/**
 * Perakit data ujian TOEFL untuk pengujian (mirip BuildsSibelData).
 *
 * Modul TOEFL kini TANPA registrasi & login: peserta cukup menulis nama +
 * kelas, lalu kode akses. Karena itu rakitan data memakai identitas yang
 * diketik peserta (`enterExam`) — bukan akun (`loginAsStudent` sudah dihapus).
 */
trait BuildsToeflData
{
    protected function createToeflRoles(): void
    {
        foreach (['super_admin' => 'Super Admin', 'admin' => 'Admin', 'admin_toefl' => 'Admin TOEFL'] as $slug => $name) {
            Role::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }

    /**
     * Buat ujian berisi 3 bagian + soal (n soal per bagian, kunci diketahui).
     *
     * Durasi bagian mengikuti spesifikasi baku di config/toefl.php:
     * Listening 50/35 menit, Structure 40/25 menit, Reading 50/55 menit.
     *
     * @return array{exam: ToeflExam, sections: array<string, ToeflSection>}
     */
    protected function buildExam(int $questionsPerSection = 2, array $overrides = []): array
    {
        $exam = ToeflExam::create(array_merge([
            'title' => 'TOEFL Uji Coba',
            'status' => ToeflExam::STATUS_PUBLISHED,
            'start_at' => now()->subHour(),
            'end_at' => now()->addDay(),
            'show_result_to_student' => true,
            'allow_review' => true,
            'duration_seconds' => 3600,
            // Unik per pembuatan — uji bisa merangkai banyak ujian dalam satu metode.
            'exam_code' => strtoupper(substr(md5((string) uniqid('', true)), 0, 8)),
        ], $overrides));

        $sections = [];
        foreach (config('toefl.sections') as $type => $def) {
            $section = $exam->sections()->create([
                'section_type' => $type,
                'title' => $def['label'],
                'duration_seconds' => $def['default_duration'],
                'section_order' => $def['order'],
            ]);

            for ($i = 1; $i <= $questionsPerSection; $i++) {
                // Kunci selalu 'A' untuk memudarkan asersi; jawaban lain di bawah.
                $section->questions()->create([
                    'question_type' => 'multiple_choice',
                    'question_text' => "Soal {$type} nomor {$i}?",
                    'options' => ['A' => 'Benar', 'B' => 'Salah', 'C' => 'Mungkin', 'D' => 'Tidak pasti'],
                    'answer_key' => 'A',
                    'points' => 1,
                    'question_order' => $i,
                ]);
            }

            $sections[$type] = $section;
        }

        $exam->refresh()->update([
            'total_questions' => $exam->questionCount(),
            'duration_seconds' => (int) $exam->sections()->sum('duration_seconds'),
        ]);

        return ['exam' => $exam->refresh(), 'sections' => $sections];
    }

    /**
     * Peserta terdaftar lebih dulu lewat panel admin (opsional — jalur utama
     * membuat identitas otomatis saat peserta masuk portal).
     */
    protected function buildStudent(string $name = 'Siswa Uji', array $overrides = []): ToeflStudent
    {
        return ToeflStudent::create(array_merge([
            'full_name' => $name,
            'student_number' => 'UJI-'.random_int(1000, 9999),
            'class_name' => 'X-1',
            'status' => ToeflStudent::STATUS_ACTIVE,
        ], $overrides));
    }

    /**
     * Terbitkan kode akses (level ujian — mengembalikan kode plaintext untuk diuji).
     */
    protected function issueCode(ToeflExam $exam): string
    {
        return app(ToeflAccessCodeService::class)->issue($exam)['code'];
    }

    /**
     * Masuk lewat identitas + kode seperti peserta nyata di portal.
     * Identitas default dipakai terus sehingga masuk ulang melanjutkan attempt.
     *
     * @return ToeflAttempt attempt terikat session
     */
    protected function enterExam(
        string $code,
        ToeflExam $exam,
        string $name = 'Siswa Uji',
        string $className = 'X-1',
    ): ToeflAttempt {
        $this->post(route('toefl.enter'), [
            'full_name' => $name,
            'class_name' => $className,
            'exam_id' => $exam->id,
            'code' => $code,
        ])->assertSessionHas('toefl_attempt_id');

        return ToeflAttempt::findOrFail(session('toefl_attempt_id'));
    }

    /**
     * Mulai waktu ujian (set started_at + deadline dari server).
     */
    protected function beginExam(ToeflAttempt $attempt): ToeflAttempt
    {
        $this->post(route('toefl.begin'))->assertRedirect(route('toefl.exam.section', 'listening'));

        return $attempt->refresh();
    }

    /**
     * Audio aset untuk sebuah bagian.
     */
    protected function buildAudio(ToeflSection $section): ToeflAudioAsset
    {
        return $section->audioAssets()->create([
            'label' => 'Audio contoh',
            'storage_path' => 'toefl/audio/sample-'.random_int(1, 99999).'.mp3',
            'mime_type' => 'audio/mpeg',
            'size_bytes' => 2048,
        ]);
    }

    /**
     * Semua soal ujian (untuk perhitungan skor manual pada asersi).
     *
     * @return Collection<int, ToeflQuestion>
     */
    protected function allQuestions(ToeflExam $exam)
    {
        return ToeflQuestion::whereIn(
            'section_id',
            $exam->sections()->pluck('id')
        )->orderBy('id')->get();
    }
}
