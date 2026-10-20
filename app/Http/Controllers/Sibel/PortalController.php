<?php

namespace App\Http\Controllers\Sibel;

use App\Http\Controllers\Controller;
use App\Models\SibelClass;
use App\Models\SibelStudent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Pintu masuk SIBEL tanpa login.
 *
 * Siswa cukup memilih identitasnya (kelas + nama) — sesi browser
 * (session) mengikat siapa yang sedang mengisi, sehingga halaman
 * kuesioner tidak lagi membutuhkan akun login.
 */
class PortalController extends Controller
{
    /**
     * Tamu -> pemilih identitas siswa.
     * Pengelola (admin/guru BK) -> dashboard pengelola.
     * Sesi siswa yang sudah terikat -> langsung ke dashboard kuesioner.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Pengelola tetap masuk ke dashboard SIBEL miliknya.
        if ($user && $user->hasRole('super_admin', 'admin', 'guru_bk')) {
            return redirect()->route('sibel.dashboard');
        }

        // Akun siswa lama (warisan sistem login) otomatis terikat ke profilnya.
        if ($user && $user->hasRole('siswa')) {
            $student = SibelStudent::where('user_id', $user->id)
                ->where('status', SibelStudent::STATUS_ACTIVE)
                ->first();

            if ($student) {
                $request->session()->put('sibel_student_id', $student->id);

                return redirect()->route('sibel.assessment.index');
            }
        }

        // Sesi yang sudah memilih identitas -> langsung ke kuesioner.
        if ($request->session()->has('sibel_student_id')) {
            return redirect()->route('sibel.assessment.index');
        }

        $classes = SibelClass::orderBy('name')->get();

        $students = SibelStudent::query()
            ->with('schoolClass')
            ->where('status', SibelStudent::STATUS_ACTIVE)
            ->when($request->filled('kelas'), fn ($q) => $q->where('class_id', $request->integer('kelas')))
            ->orderBy('full_name')
            ->get(['id', 'class_id', 'student_number', 'full_name']);

        return view('sibel.portal', compact('classes', 'students'));
    }

    /**
     * Ikat identitas siswa ke sesi browser, lalu buka dashboard kuesioner.
     */
    public function identify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer'],
        ], [
            'student_id.required' => 'Pilih nama Anda terlebih dahulu.',
            'student_id.integer' => 'Pilih nama Anda terlebih dahulu.',
        ]);

        $student = SibelStudent::with('schoolClass')
            ->where('status', SibelStudent::STATUS_ACTIVE)
            ->find($validated['student_id']);

        if (! $student) {
            return back()
                ->withInput()
                ->withErrors(['student_id' => 'Nama tidak ditemukan atau sudah tidak aktif. Silakan hubungi guru BK.']);
        }

        $request->session()->put('sibel_student_id', $student->id);

        return redirect()->route('sibel.assessment.index')
            ->with('success', "Selamat datang, {$student->full_name}! Silakan mengisi kuesioner.");
    }

    /**
     * Lepas ikatan identitas (tombol "Ganti Siswa") — penting bila satu
     * perangkat dipakai bergantian oleh banyak siswa.
     */
    public function forget(Request $request): RedirectResponse
    {
        $request->session()->forget('sibel_student_id');

        return redirect()->route('sibel.portal')
            ->with('success', 'Identitas siswa telah dilepas. Silakan pilih nama siswa berikutnya.');
    }
}
