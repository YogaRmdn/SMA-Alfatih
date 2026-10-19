<?php

namespace Database\Seeders;

use App\Models\SibelOption;
use App\Models\SibelQuestion;
use App\Models\SibelQuestionnaire;
use Illuminate\Database\Seeder;

/**
 * Kuesioner bawaan SIBEL (versi awal).
 *
 * PENTING: seluruh pertanyaan dan pemetaan A=Visual, B=Auditori,
 * C=Kinestetik di sini adalah KONFIGURASI AWAL yang BELUM diverifikasi
 * terhadap dokumen kuesioner asli. Kuesioner sengaja dibuat berstatus
 * `draft` dengan penanda "Perlu Verifikasi". Jangan mengubah bunyi
 * pertanyaan tanpa persetujuan; setelah diverifikasi, buka sesinya
 * lewat panel SIBEL.
 */
class SibelSeeder extends Seeder
{
    /**
     * @return array<int, array{question: string, options: array<string, string>}>
     */
    protected function questions(): array
    {
        return [
            [
                'question' => 'Ketika mempelajari hal baru, saya lebih suka...',
                'options' => [
                    'A' => 'membaca teks bergambar, diagram, atau skema',
                    'B' => 'mendengarkan penjelasan lisan dari guru',
                    'C' => 'langsung mencoba atau mempraktikkannya',
                ],
            ],
            [
                'question' => 'Saya lebih mudah mengingat materi ketika...',
                'options' => [
                    'A' => 'saya membuat catatan terstruktur atau ringkasan visual',
                    'B' => 'saya mendengarkan dan mengulang penjelasan dengan kata-kata sendiri',
                    'C' => 'saya mengalami sendiri materi tersebut melalui kegiatan',
                ],
            ],
            [
                'question' => 'Saat mengerjakan tugas sulit, langkah pertama saya adalah...',
                'options' => [
                    'A' => 'membuat peta konsep atau kerangka gambar masalah',
                    'B' => 'berdiskusi atau bertanya kepada orang lain',
                    'C' => 'langsung mengerjakan sambil mencoba-coba',
                ],
            ],
            [
                'question' => 'Cara saya paling nyaman belajar di rumah adalah...',
                'options' => [
                    'A' => 'membaca buku, modul, dan melihat ilustrasi',
                    'B' => 'mendengarkan audio, video, atau menjelaskan materi ke orang lain',
                    'C' => 'mengerjakan latihan sambil bergerak atau memakai alat bantu',
                ],
            ],
            [
                'question' => 'Ketika presentasi di kelas, saya paling mudah memahami...',
                'options' => [
                    'A' => 'slide dengan gambar, warna, dan bagan yang jelas',
                    'B' => 'penjelasan lisan presenter dan tanya jawab',
                    'C' => 'demonstrasi langsung atau praktik di depan kelas',
                ],
            ],
            [
                'question' => 'Saat menghafal informasi penting, saya biasanya...',
                'options' => [
                    'A' => 'menyusunnya menjadi daftar, tabel, atau gambar',
                    'B' => 'mengucapkan atau mendengarkannya berulang kali',
                    'C' => 'menghubungkannya dengan gerakan atau pengalaman nyata',
                ],
            ],
            [
                'question' => 'Dalam pelajaran matematika/sains, saya paling terbantu oleh...',
                'options' => [
                    'A' => 'gambar, grafik, dan penyelesaian bertahap tertulis rapi',
                    'B' => 'penjelasan logis lisan dan diskusi jawaban',
                    'C' => 'alat peraga, eksperimen, atau soal yang dikerjakan langsung',
                ],
            ],
            [
                'question' => 'Ketika ada kata atau konsep baru, saya cenderung...',
                'options' => [
                    'A' => 'mencari gambarnya atau membuat tulisan sendiri',
                    'B' => 'menyebutkan artinya dengan kalimat sendiri',
                    'C' => 'mencari contoh nyata dan mencobanya',
                ],
            ],
            [
                'question' => 'Saat membaca buku pelajaran, saya lebih menikmati...',
                'options' => [
                    'A' => 'buku dengan banyak gambar, bagan, dan penataan visual menarik',
                    'B' => 'buku yang bisa saya diskusikan atau ceritakan kembali',
                    'C' => 'buku yang memuat latihan dan kegiatan praktis',
                ],
            ],
            [
                'question' => 'Jika saya melewatkan penjelasan di kelas, cara terbaik mengikuti pelajaran berikutnya adalah...',
                'options' => [
                    'A' => 'meminjam catatan teman dan membaca ulang materinya',
                    'B' => 'meminta teman menjelaskan kembali dengan kata-katanya',
                    'C' => 'mengerjakan latihan soal untuk mengingat kembali materi',
                ],
            ],
            [
                'question' => 'Saya paling fokus belajar ketika...',
                'options' => [
                    'A' => 'ruangan tenang dan catatan saya rapi serta berwarna',
                    'B' => 'ada orang yang bisa diajak berbincang tentang materi',
                    'C' => 'saya bisa bergerak dan memegang benda-benda terkait materi',
                ],
            ],
            [
                'question' => 'Saat menghadapi soal yang belum pernah dikerjakan, saya...',
                'options' => [
                    'A' => 'menjabarkan informasinya menjadi gambar atau bagan dulu',
                    'B' => 'membandingkan dengan contoh yang pernah didengar/dijelaskan',
                    'C' => 'mencoba beberapa cara langsung sampai ketemu polanya',
                ],
            ],
            [
                'question' => 'Teknik belajar yang paling sering saya gunakan adalah...',
                'options' => [
                    'A' => 'menyusun flashcard, warnai, dan buat ringkasan visual',
                    'B' => 'berdiskusi, mengajari teman, atau mendengarkan rekaman',
                    'C' => 'praktik, simulasi, dan belajar sambil bergerak',
                ],
            ],
            [
                'question' => 'Ketika guru menjelaskan materi baru, saya paling mudah mengikuti jika...',
                'options' => [
                    'A' => 'ada gambar, contoh tertulis, atau alur di papan tulis',
                    'B' => 'dijelaskan pelan-pelan dan diulangi dengan contoh lisan',
                    'C' => 'langsung diberikan contoh kasus yang bisa saya kerjakan',
                ],
            ],
            [
                'question' => 'Setelah ujian, cara saya mengevaluasi jawaban adalah...',
                'options' => [
                    'A' => 'melihat kembali catatan dan menandai bagian yang salah secara visual',
                    'B' => 'mendiskusikan jawaban dengan guru atau teman',
                    'C' => 'mengerjakan ulang soal yang salah sampai lancar',
                ],
            ],
        ];
    }

    public function run(): void
    {
        $questionnaire = SibelQuestionnaire::firstOrCreate(
            ['title' => 'Kuesioner Kecenderungan Gaya Belajar Siswa'],
            [
                'instructions' => "Petunjuk pengisian:\n"
                    ."1. Bacakan setiap pertanyaan dengan teliti.\n"
                    ."2. Pilih SATU pilihan (A, B, atau C) yang paling menggambarkan kebiasaan belajar Anda.\n"
                    ."3. Jawab dengan jujur — tidak ada jawaban yang benar atau salah.\n"
                    ."4. Perhatikan indikator progres dan pastikan seluruh pertanyaan terjawab sebelum mengirim.\n\n"
                    .'CATATAN: instrumen ini konfigurasi awal dan masih menunggu verifikasi terhadap dokumen kuesioner asli.',
                'version' => '1.0',
                'status' => SibelQuestionnaire::STATUS_DRAFT,
                'allow_retake' => false,
                'needs_verification' => true,
            ]
        );

        if ($questionnaire->questions()->exists()) {
            return;
        }

        foreach ($this->questions() as $index => $item) {
            $question = SibelQuestion::create([
                'questionnaire_id' => $questionnaire->id,
                'question_text' => $item['question'],
                'order_number' => $index + 1,
                'is_required' => true,
            ]);

            foreach (config('sibel.code_category') as $code => $categoryKey) {
                SibelOption::create([
                    'question_id' => $question->id,
                    'option_code' => $code,
                    'option_text' => $item['options'][$code],
                    'category_code' => $categoryKey,
                ]);
            }
        }

        $this->command?->info('Kuesioner SIBEL bawaan dibuat (draft, perlu verifikasi): 15 pertanyaan x 3 opsi.');
    }
}
