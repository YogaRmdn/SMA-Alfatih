<?php

namespace Database\Seeders;

use App\Models\SibelOption;
use App\Models\SibelQuestion;
use App\Models\SibelQuestionnaire;
use Illuminate\Database\Seeder;

/**
 * Kuesioner SIBEL metode Skala Likert 4 tingkat (opsi kedua selain
 * pilihan tunggal A/B/C).
 *
 * Struktur: setiap pernyataan dimiliki SATU kategori indikator
 * (visual/auditori/kinestetik) dan dijawab dengan skala
 * Sangat Sesuai (4) s.d. Tidak Sesuai (1). Skor kategori =
 * jumlah nilai seluruh pernyataannya.
 *
 * PENTING: bunyi pernyataan adalah KONFIGURASI AWAL yang BELUM diverifikasi
 * terhadap dokumen kuesioner asli. Kuesioner sengaja dibuat berstatus
 * `draft` dengan penanda "Perlu Verifikasi" — verifikasi dulu sebelum dibuka.
 */
class SibelLikertSeeder extends Seeder
{
    /**
     * @return array<int, array{category: string, statement: string}>
     */
    protected function statements(): array
    {
        return [
            // Visual (5 pernyataan).
            ['category' => 'visual', 'statement' => 'Saya lebih mudah memahami materi ketika disertai gambar, diagram, atau skema.'],
            ['category' => 'visual', 'statement' => 'Saya membuat catatan terstruktur, peta konsep, atau ringkasan visual saat belajar.'],
            ['category' => 'visual', 'statement' => 'Saya mengingat informasi penting lebih baik setelah menuliskannya menjadi daftar atau tabel.'],
            ['category' => 'visual', 'statement' => 'Slide presentasi dengan warna dan bagan yang rapi membantu saya mengikuti pelajaran.'],
            ['category' => 'visual', 'statement' => 'Saya lebih menikmati buku pelajaran yang banyak memuat gambar dan ilustrasi.'],

            // Auditori (5 pernyataan).
            ['category' => 'auditori', 'statement' => 'Saya lebih mudah memahami penjelasan lisan guru daripada membaca teks sendiri.'],
            ['category' => 'auditori', 'statement' => 'Saya mengingat materi lebih baik ketika mendengarkan dan mengulangnya dengan kata-kata sendiri.'],
            ['category' => 'auditori', 'statement' => 'Saya suka berdiskusi atau menjelaskan materi kepada orang lain untuk menguji pemahaman.'],
            ['category' => 'auditori', 'statement' => 'Mendengarkan audio, rekaman, atau penjelasan video membantu saya belajar.'],
            ['category' => 'auditori', 'statement' => 'Saya lebih fokus belajar ketika ada orang yang bisa diajak berbincang tentang materi.'],

            // Kinestetik (5 pernyataan).
            ['category' => 'kinestetik', 'statement' => 'Saya lebih suka langsung mencoba atau mempraktikkan hal baru daripada hanya membacanya.'],
            ['category' => 'kinestetik', 'statement' => 'Saya mengingat materi lebih baik ketika mengalaminya sendiri melalui kegiatan.'],
            ['category' => 'kinestetik', 'statement' => 'Alat peraga, eksperimen, dan praktik langsung membuat saya cepat memahami konsep.'],
            ['category' => 'kinestetik', 'statement' => 'Saya senang belajar sambil bergerak atau memegang benda-benda terkait materi.'],
            ['category' => 'kinestetik', 'statement' => 'Saya menyelesaikan soal sulit dengan mencoba beberapa cara langsung sampai menemukan polanya.'],
        ];
    }

    public function run(): void
    {
        $questionnaire = SibelQuestionnaire::firstOrCreate(
            ['title' => 'Kuesioner Gaya Belajar — Skala Likert'],
            [
                'instructions' => "Petunjuk pengisian:\n"
                    ."1. Bacakan setiap pernyataan dengan teliti.\n"
                    ."2. Pilih SATU jawaban yang paling menggambarkan kebiasaan belajar Anda:\n"
                    ."   Sangat Sesuai / Sesuai / Kurang Sesuai / Tidak Sesuai.\n"
                    ."3. Jawab dengan jujur — tidak ada jawaban yang benar atau salah.\n"
                    ."4. Pastikan seluruh pernyataan terjawab sebelum mengirim.\n\n"
                    .'CATATAN: instrumen ini konfigurasi awal dan masih menunggu verifikasi terhadap dokumen kuesioner asli.',
                'version' => '2.0',
                'scoring_method' => SibelQuestionnaire::SCORING_METHOD_LIKERT,
                'status' => SibelQuestionnaire::STATUS_DRAFT,
                'allow_retake' => false,
                'needs_verification' => true,
            ]
        );

        if ($questionnaire->questions()->exists()) {
            return;
        }

        foreach ($this->statements() as $index => $item) {
            $question = SibelQuestion::create([
                'questionnaire_id' => $questionnaire->id,
                'question_text' => $item['statement'],
                'category_code' => $item['category'],
                'order_number' => $index + 1,
                'is_required' => true,
            ]);

            // Opsi skala Likert seragam dari konfigurasi (kode, label, nilai).
            foreach (config('sibel.likert.levels') as $level) {
                SibelOption::create([
                    'question_id' => $question->id,
                    'option_code' => $level['code'],
                    'option_text' => $level['label'],
                    'value' => $level['value'],
                ]);
            }
        }

        $this->command?->info('Kuesioner SIBEL Likert dibuat (draft, perlu verifikasi): 15 pernyataan x 4 opsi skala.');
    }
}
