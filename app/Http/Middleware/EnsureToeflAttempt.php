<?php

namespace App\Http\Middleware;

use App\Models\ToeflAttempt;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ikat attempt TOEFL aktif ke session browser (`toefl_attempt_id`).
 *
 * Modul TOEFL berjalan TANPA registrasi & login: peserta mengisi nama + kelas
 * lalu kode akses, dan server mengikat attempt-nya ke session browser. Karena
 * session disimpan di server, `toefl_attempt_id` TIDAK bisa dipalsukan lewat
 * browser — route di bawah middleware ini hanya terbuka bagi pemegang attempt
 * yang terikat pada sesi ini.
 *
 * (Dulu ada pula pemeriksaan kepemilikan terhadap akun guard `toefl`; akun
 * tersebut sudah dihapus bersama alur login.)
 */
class EnsureToeflAttempt
{
    public function handle(Request $request, Closure $next): Response
    {
        $attemptId = (int) $request->session()->get('toefl_attempt_id');

        if ($attemptId <= 0) {
            // Endpoint JSON (auto-save/audio/peristiwa) memakai respons JSON,
            // bukan redirect halaman.
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Sesi ujian belum dimulai. Masukkan kode akses Anda terlebih dahulu.',
                ], 401);
            }

            return redirect()->route('toefl.portal')
                ->with('error', 'Sesi ujian belum dimulai. Silakan isi nama, kelas, dan kode akses Anda terlebih dahulu.');
        }

        $attempt = ToeflAttempt::with(['exam.sections', 'student'])
            ->find($attemptId);

        if (! $attempt) {
            $request->session()->forget('toefl_attempt_id');

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Sesi ujian tidak ditemukan. Silakan masukkan ulang kode akses Anda.',
                ], 401);
            }

            return redirect()->route('toefl.portal')
                ->with('error', 'Sesi ujian tidak ditemukan. Silakan masukkan ulang kode akses Anda.');
        }

        // Attempt yang sudah dinilai & punya hasil -> hanya boleh lihat hasil.
        if ($attempt->isFinished() && ! $request->routeIs('toefl.result*', 'toefl.leave')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'expired' => true,
                    'message' => 'Ujian Anda sudah dikumpulkan.',
                    'redirect' => route('toefl.result'),
                ], 422);
            }

            return redirect()->route('toefl.result')
                ->with('info', 'Ujian Anda sudah dikumpulkan. Berikut hasilnya.');
        }

        $request->attributes->set('toefl_attempt', $attempt);

        return $next($request);
    }
}
