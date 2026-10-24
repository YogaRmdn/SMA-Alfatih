<?php

namespace App\Http\Controllers\Toefl;

use App\Http\Controllers\Controller;
use App\Http\Requests\ToeflAccessRequest;
use App\Models\ToeflAccessCode;
use App\Models\ToeflAttempt;
use App\Models\ToeflExam;
use App\Models\ToeflStudent;
use App\Services\ToeflAccessCodeService;
use App\Services\ToeflAttemptService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

/**
 * Portal TOEFL — TANPA registrasi & login (alur sama seperti SIBEL).
 *
 * Alur peserta: buka /toefl -> tulis nama lengkap + kelas, pilih ujian,
 * masukkan kode akses -> server cari/buat identitas lalu buat/resume attempt
 * (diikat ke session browser) -> instruksi -> konfirmasi "Mulai" -> deadline
 * diset dari timestamp server -> kerjakan 3 bagian -> kumpulkan -> lihat hasil.
 *
 * Tidak ada akun siswa: identitas murni dari input peserta, dan "Keluar Sesi"
 * cukup melepas ikatan session sehingga perangkat bisa dipakai peserta lain.
 */
class PortalController extends Controller
{
    public function __construct(
        protected ToeflAccessCodeService $codes,
        protected ToeflAttemptService $attempts,
    ) {}

    /**
     * Halaman portal `/toefl`: form identitas + kode, sesi yang masih
     * berlangsung, dan riwayat milik identitas yang terikat session ini.
     * Pengelola (admin/guru BK) dialihkan ke panel kelola TOEFL.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->hasRole('super_admin', 'admin', 'admin_toefl')) {
            return redirect()->route('toefl.admin.dashboard');
        }

        $exams = $this->openExams();

        $student = $this->boundStudent($request);

        $activeAttempt = null;
        $attempts = collect();

        if ($student) {
            $activeAttempt = ToeflAttempt::with('exam')
                ->where('student_id', $student->id)
                ->where('status', ToeflAttempt::STATUS_IN_PROGRESS)
                ->latest('updated_at')
                ->first();

            $attempts = ToeflAttempt::with(['exam', 'result'])
                ->where('student_id', $student->id)
                ->orderByDesc('id')
                ->limit(20)
                ->get();
        }

        return view('toefl.portal', [
            'student' => $student,
            'exams' => $exams,
            'activeAttempt' => $activeAttempt,
            'attempts' => $attempts,
        ]);
    }

    /**
     * Validasi kode akses -> cari/buat identitas peserta -> buat/resume
     * attempt -> ikat identitas + attempt ke session browser ini.
     * Dilindungi throttle untuk membatasi percobaan menebak kode.
     */
    public function enter(ToeflAccessRequest $request): RedirectResponse
    {
        $exam = ToeflExam::find($request->validated('exam_id'));

        $result = $this->codes->validate($request->validated('code'), $exam);

        if (isset($result['error'])) {
            return back()->withErrors(['code' => $result['error']])->withInput();
        }

        $student = $this->resolveStudent(
            $request->validated('full_name'),
            $request->validated('class_name'),
        );

        try {
            $attempt = $this->attempts->startFromCode($result['code'], $student);
        } catch (RuntimeException $e) {
            return back()->withErrors(['code' => $e->getMessage()])->withInput();
        }

        // Regenerate mencegah session fixation sebelum mengikat sesi ujian.
        $request->session()->regenerate();
        $request->session()->put([
            'toefl_student_id' => $student->id,
            'toefl_attempt_id' => $attempt->id,
        ]);

        // Sudah pernah mulai & masih berjalan -> lanjut; belum -> instruksi.
        if ($attempt->started_at === null) {
            return redirect()->route('toefl.instructions');
        }

        if ($attempt->isFinished()) {
            return redirect()->route('toefl.result');
        }

        if ($attempt->hasExpired()) {
            $this->attempts->finalize($attempt, ToeflAttempt::STATUS_EXPIRED);

            return redirect()->route('toefl.result');
        }

        return redirect()->route('toefl.exam.section', $attempt->current_section_type ?: 'listening');
    }

    /**
     * Masuk cepat sebagai "Peserta Demo" — memakai logika yang sama dengan
     * `enter()` (cari/buat identitas, buat/resume attempt, ikat session),
     * hanya saja kode aksesnya diminta sendiri oleh server.
     *
     * Fitur PENGEMBANGAN LOKAL: method ini menolak dirinya sendiri (404)
     * bila APP_DEBUG=false, sehingga jalur melewati kode tidak pernah ada
     * di produksi.
     */
    public function demo(Request $request): RedirectResponse
    {
        abort_unless(config('app.debug'), 404);

        $exams = $this->openExams();

        // Utamakan ujian yang sudah benar-benar punya soal supaya langsung
        // kelihatan tampilan pengerjaan.
        $exam = $exams->first(fn (ToeflExam $item) => $item->questionCount() > 0) ?? $exams->first();

        if (! $exam) {
            return back()->withErrors(['code' => 'Belum ada ujian TOEFL yang sedang dibuka.']);
        }

        $student = $this->resolveStudent('Peserta Demo', 'Demo');
        $code = $this->demoCode($exam);

        try {
            $attempt = $this->attempts->startFromCode($code, $student);
        } catch (RuntimeException $e) {
            return back()->withErrors(['code' => $e->getMessage()]);
        }

        $request->session()->regenerate();
        $request->session()->put([
            'toefl_student_id' => $student->id,
            'toefl_attempt_id' => $attempt->id,
        ]);

        if ($attempt->started_at === null) {
            return redirect()->route('toefl.instructions');
        }

        if ($attempt->isFinished()) {
            return redirect()->route('toefl.result');
        }

        if ($attempt->hasExpired()) {
            $this->attempts->finalize($attempt, ToeflAttempt::STATUS_EXPIRED);

            return redirect()->route('toefl.result');
        }

        return redirect()->route('toefl.exam.section', $attempt->current_section_type ?: 'listening');
    }

    /**
     * Ujian yang sedang bisa diikuti (status terbit + dalam jadwal).
     *
     * @return Collection<int, ToeflExam>
     */
    protected function openExams()
    {
        return ToeflExam::query()
            ->where('status', ToeflExam::STATUS_PUBLISHED)
            ->where(function ($q) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', now());
            })
            ->orderBy('start_at')
            ->get();
    }

    /**
     * Kode akses khusus demo, deterministik per ujian (DEMO0002, dst.) sehingga
     * tombol bisa dipakai berulang kali tanpa membanjiri tabel kode akses.
     * Kode ini TIDAK pernah dibuat bila APP_DEBUG=false.
     */
    protected function demoCode(ToeflExam $exam): ToeflAccessCode
    {
        $raw = 'DEMO'.str_pad((string) $exam->id, 4, '0', STR_PAD_LEFT);

        return ToeflAccessCode::firstOrCreate(
            ['exam_id' => $exam->id, 'code_hash' => hash('sha256', $raw)],
            ['code_prefix' => 'DEMO'],
        );
    }

    /**
     * Halaman instruksi sebelum waktu dimulai.
     */
    public function instructions(Request $request): View|RedirectResponse
    {
        $attempt = $this->boundAttempt($request);

        if (! $attempt) {
            return redirect()->route('toefl.portal');
        }

        if ($attempt->started_at !== null || $attempt->isFinished()) {
            return $this->continueAfterStart($attempt);
        }

        $attempt->load('exam.sections');

        $instructions = array_map(
            fn (string $line) => str_replace(
                '{audio_limit}',
                (string) config('toefl.audio.max_plays', 2),
                $line
            ),
            config('toefl.instructions', [])
        );

        return view('toefl.instructions', [
            'attempt' => $attempt,
            'exam' => $attempt->exam,
            'instructions' => $instructions,
        ]);
    }

    /**
     * Konfirmasi "Mulai Ujian" -> setel started_at & deadline dari jam server.
     */
    public function begin(Request $request): RedirectResponse
    {
        $attempt = $this->boundAttempt($request);

        if (! $attempt) {
            return redirect()->route('toefl.portal');
        }

        if (! $attempt->exam->isOpen()) {
            return redirect()->route('toefl.portal')
                ->with('error', 'Ujian sudah ditutup. Hubungi pengawas ujian.');
        }

        $this->attempts->begin($attempt);

        return redirect()->route('toefl.exam.section', 'listening');
    }

    /**
     * Lepas ikatan identitas + attempt dari session browser (tombol
     * "Keluar Sesi") — penting bila satu komputer dipakai bergantian
     * oleh banyak peserta.
     */
    public function leave(Request $request): RedirectResponse
    {
        $request->session()->forget(['toefl_attempt_id', 'toefl_student_id']);

        return redirect()->route('toefl.portal')
            ->with('success', 'Sesi ujian dilepas dari perangkat ini. Silakan peserta berikutnya mengisi identitasnya.');
    }

    /**
     * Identitas peserta yang terikat session browser ini (atau null).
     */
    protected function boundStudent(Request $request): ?ToeflStudent
    {
        $studentId = (int) $request->session()->get('toefl_student_id');

        if ($studentId <= 0) {
            return null;
        }

        return ToeflStudent::find($studentId);
    }

    /**
     * Attempt yang terikat pada session browser ini (atau null).
     */
    protected function boundAttempt(Request $request): ?ToeflAttempt
    {
        $attemptId = (int) $request->session()->get('toefl_attempt_id');

        if ($attemptId <= 0) {
            return null;
        }

        return ToeflAttempt::with('exam')->find($attemptId);
    }

    /**
     * Cari identitas peserta berdasarkan nama yang ditulis (huruf
     * besar/kecil tidak berpengaruh) + kelas yang ditulis:
     *   1. cocok nama & kelas -> pakai identitas itu (attempt lama ikut
     *      terpakai ulang — satu attempt per peserta per ujian);
     *   2. belum ada           -> buat identitas baru.
     *
     * Status nonaktif diperiksa pemanggil (`startFromCode`) agar peserta
     * yang diblokir pengawas tetap tertolak.
     */
    protected function resolveStudent(string $name, string $className): ToeflStudent
    {
        $student = ToeflStudent::query()
            ->whereRaw('LOWER(full_name) = ?', [mb_strtolower($name)])
            ->whereRaw('LOWER(COALESCE(class_name, \'\')) = ?', [mb_strtolower($className)])
            ->orderBy('id')
            ->first();

        if ($student) {
            return $student;
        }

        return ToeflStudent::create([
            'full_name' => $name,
            'class_name' => $className,
            'status' => ToeflStudent::STATUS_ACTIVE,
        ]);
    }

    protected function continueAfterStart(ToeflAttempt $attempt): RedirectResponse
    {
        if ($attempt->isFinished()) {
            return redirect()->route('toefl.result');
        }

        if ($attempt->hasExpired()) {
            $this->attempts->finalize($attempt, ToeflAttempt::STATUS_EXPIRED);

            return redirect()->route('toefl.result');
        }

        return redirect()->route('toefl.exam.section', $attempt->current_section_type ?: 'listening');
    }
}
