<?php

namespace App\Services;

use App\Models\ToeflAccessCode;
use App\Models\ToeflExam;
use Illuminate\Support\Str;

/**
 * Kode akses TOEFL — kunci masuk peserta (TANPA registrasi & login).
 *
 * Modul berjalan seperti SIBEL: peserta menulis nama + kelas di portal,
 * lalu memasukkan kode. Karena itu kode bersifat LEVEL UJIAN (boleh dipakai
 * peserta mana pun yang tahu kodenya) — identitas peserta baru dibuat saat
 * mereka masuk, sehingga kode tidak terikat ke satu orang sejak awal.
 *
 * Keamanan:
 *  - Kode asli TIDAK pernah disimpan — hanya hash (SHA-256) + prefix kecil
 *    untuk pencarian admin.
 *  - Validasi sepenuhnya di server (status dicabut/kedaluwarsa + jadwal ujian).
 *  - Rate limiting dilakukan pada route masuk (`toefl.enter`, throttle).
 */
class ToeflAccessCodeService
{
    public const CODE_LENGTH = 8;

    /**
     * Format tampilan kode: XXXX-XXXX (lebih mudah dibaca/diketik siswa).
     */
    public static function format(string $raw): string
    {
        return Str::upper(Str::substr($raw, 0, 4).'-'.Str::substr($raw, 4, 4));
    }

    /**
     * Terbitkan kode akses baru untuk sebuah ujian (terbuka untuk siapa pun
     * yang memegang kode — identitas peserta diisi saat mereka masuk).
     *
     * @return array{code: string, model: ToeflAccessCode} kode tampil (PLAINTEXT,
     *                                                     hanya dikembalikan sekali saat pembuatan)
     */
    public function issue(ToeflExam $exam, ?\DateTimeInterface $expiresAt = null): array
    {
        $raw = $this->generateUniqueCode($exam);

        $code = ToeflAccessCode::create([
            'exam_id' => $exam->id,
            'code_hash' => $this->hash($raw),
            'code_prefix' => Str::upper(Str::substr($raw, 0, 4)),
            'expires_at' => $expiresAt,
        ]);

        return ['code' => self::format($raw), 'model' => $code];
    }

    /**
     * Validasi kode mentah (tanpa tanda hubung) terhadap sebuah ujian.
     *
     * @return array{error: string}|array{code: ToeflAccessCode}
     */
    public function validate(string $rawCode, ToeflExam $exam): array
    {
        $normalized = Str::upper(str_replace('-', '', trim($rawCode)));

        if (strlen($normalized) !== self::CODE_LENGTH) {
            return ['error' => 'Kode akses tidak valid. Periksa kembali penulisan kode Anda.'];
        }

        $code = ToeflAccessCode::with('exam')
            ->where('exam_id', $exam->id)
            ->where('code_hash', $this->hash($normalized))
            ->first();

        if (! $code) {
            return ['error' => 'Kode akses tidak ditemukan untuk ujian ini.'];
        }

        if ($reason = $code->invalidReason()) {
            return ['error' => $reason];
        }

        return ['code' => $code];
    }

    protected function hash(string $raw): string
    {
        return hash('sha256', $raw);
    }

    /**
     * Kode acak 8 karakter (huruf + angka, tanpa karakter ambigu) — unik per ujian.
     */
    protected function generateUniqueCode(ToeflExam $exam): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // tanpa I, O, 0, 1

        do {
            $raw = '';
            for ($i = 0; $i < self::CODE_LENGTH; $i++) {
                $raw .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while (ToeflAccessCode::where('exam_id', $exam->id)
            ->where('code_hash', $this->hash($raw))
            ->exists());

        return $raw;
    }
}
