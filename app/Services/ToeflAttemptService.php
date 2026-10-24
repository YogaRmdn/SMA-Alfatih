<?php

namespace App\Services;

use App\Models\ToeflAccessCode;
use App\Models\ToeflAttempt;
use App\Models\ToeflResult;
use App\Models\ToeflStudent;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Sesi ujian TOEFL — pembuatan/resume attempt + penegakan deadline server.
 *
 * PRINSIP KEAMANAN:
 *  - Deadline dihitung & disimpan dari timestamp SERVER saat siswa menekan
 *    "Mulai Ujian". Jam perangkat siswa TIDAK PERNAH dipercai.
 *  - Maksimal satu attempt per (siswa, ujian) — dicegah oleh unique index,
 *    sehingga percobaan sesi ganda/konkuren ditolak database.
 *  - Koneksi terputus -> resume attempt yang sama (tanpa attempt ganda).
 *  - Attempt lewat deadline otomatis difinalisasi (dinilai) pada interaksi
 *    berikutnya — jawaban yang masuk setelah deadline ditolak.
 *
 * ALUR: peserta isi nama + kelas -> kode divalidasi -> identitas dicari/dibuat
 * -> attempt dibuat (belum berjalan) -> peserta membaca instruksi -> konfirmasi
 * "Mulai" -> `begin()` menyetel started_at/deadline.
 */
class ToeflAttemptService
{
    public function __construct(protected ToeflScoringService $scoring) {}

    /**
     * Buat ATAU resume attempt untuk seorang peserta pada sebuah kode
     * (belum dimulai waktunya).
     *
     * @throws RuntimeException bila ujian tidak dibuka / sudah ditutup /
     *                          identitas peserta tidak aktif
     */
    public function startFromCode(ToeflAccessCode $code, ToeflStudent $student): ToeflAttempt
    {
        $exam = $code->exam;

        if (! $exam || ! $exam->isOpen()) {
            throw new RuntimeException('Ujian ini belum dibuka atau sudah ditutup. Silakan hubungi pengawas.');
        }

        if (! $student->isActive()) {
            throw new RuntimeException('Data peserta atas nama ini tidak aktif. Silakan hubungi pengawas.');
        }

        return DB::transaction(function () use ($code, $student) {
            // Satu attempt per (peserta, ujian) — dicegah unique index, sehingga
            // peserta yang masuk ulang dengan nama & kelas sama melanjutkan sesi.
            $attempt = ToeflAttempt::where('exam_id', $code->exam_id)
                ->where('student_id', $student->id)
                ->lockForUpdate()
                ->first();

            if ($attempt) {
                return $this->resume($attempt);
            }

            $attempt = ToeflAttempt::create([
                'exam_id' => $code->exam_id,
                'student_id' => $student->id,
                'access_code_id' => $code->id,
                'status' => ToeflAttempt::STATUS_IN_PROGRESS,
                'session_version' => 1,
            ]);

            $code->update(['used_at' => now()]);

            return $attempt;
        });
    }

    /**
     * Mulai penghitungan waktu: set started_at + deadline dari timestamp server.
     * Idempoten — attempt yang sudah berjalan tidak disetel ulang.
     */
    public function begin(ToeflAttempt $attempt): ToeflAttempt
    {
        if (! $attempt->isInProgress() || $attempt->started_at !== null) {
            return $attempt;
        }

        $exam = $attempt->exam;
        $totalDuration = $exam->duration_seconds > 0
            ? $exam->duration_seconds
            : (int) $exam->sections()->sum('duration_seconds');

        $grace = (int) config('toefl.timer.grace_seconds', 30);

        $attempt->update([
            'started_at' => now(),
            'deadline_at' => now()->addSeconds($totalDuration + $grace),
        ]);

        return $attempt->refresh();
    }

    /**
     * Resume attempt yang sudah ada — bila sudah lewat deadline, finalisasi.
     */
    public function resume(ToeflAttempt $attempt): ToeflAttempt
    {
        if ($attempt->isInProgress() && $attempt->started_at !== null && $attempt->hasExpired()) {
            $this->finalize($attempt, ToeflAttempt::STATUS_EXPIRED);
        } elseif ($attempt->isInProgress()) {
            $attempt->increment('session_version');
            $attempt->refresh();
        }

        return $attempt;
    }

    /**
     * Validasi bahwa attempt masih boleh menerima jawaban.
     * Auto-finalize bila lewat deadline server.
     *
     * @throws AttemptExpiredException
     */
    public function assertActive(ToeflAttempt $attempt): void
    {
        if (! $attempt->isInProgress()) {
            throw new AttemptExpiredException('Ujian ini sudah dikumpulkan.');
        }

        if ($attempt->started_at !== null && $attempt->hasExpired()) {
            $this->finalize($attempt, ToeflAttempt::STATUS_EXPIRED);

            throw new AttemptExpiredException('Waktu ujian telah habis. Jawaban tersimpan telah dinilai otomatis.');
        }
    }

    /**
     * Finalisasi attempt: status -> submitted/expired, hitung skor, simpan hasil.
     * Idempoten — bila sudah selesai, hasil lama tidak dihitung ulang.
     *
     * @throws RuntimeException bila ujian sudah ditutup paksa oleh pengawas saat submit manual
     */
    public function finalize(ToeflAttempt $attempt, string $status = ToeflAttempt::STATUS_SUBMITTED): ?ToeflResult
    {
        if ($attempt->isFinished()) {
            return $attempt->result;
        }

        return DB::transaction(function () use ($attempt, $status) {
            $attempt->update([
                'status' => $status,
                'submitted_at' => now(),
            ]);

            return $this->scoring->scoreAndPersist($attempt->fresh());
        });
    }
}
