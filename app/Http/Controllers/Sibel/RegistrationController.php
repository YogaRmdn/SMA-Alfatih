<?php

namespace App\Http\Controllers\Sibel;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sibel\StudentRegistrationRequest;
use App\Models\Role;
use App\Models\SibelClass;
use App\Models\SibelStudent;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Halaman pendaftaran mandiri siswa (tamu).
     */
    public function create(): View
    {
        return view('sibel.auth.register', [
            'classes' => SibelClass::orderBy('name')->get(),
        ]);
    }

    /**
     * Proses pendaftaran: buat akun ber-role siswa + profil siswa,
     * lalu login otomatis dan arahkan ke halaman pengisian kuesioner.
     */
    public function store(StudentRegistrationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $user = DB::transaction(function () use ($data) {
                $role = Role::firstOrCreate(
                    ['slug' => 'siswa'],
                    ['name' => 'Siswa', 'description' => 'Mengisi kuesioner gaya belajar dan melihat hasilnya sendiri.']
                );

                $user = new User;
                $user->name = $data['full_name'];
                $user->email = $data['email'];
                $user->password = $data['password'];
                $user->email_verified_at = now();
                $user->role_id = $role->id;
                $user->save();

                SibelStudent::create([
                    'user_id' => $user->id,
                    'class_id' => $data['class_id'] ?? null,
                    'student_number' => ($data['student_number'] ?? '') !== '' ? $data['student_number'] : null,
                    'full_name' => $data['full_name'],
                    'status' => SibelStudent::STATUS_ACTIVE,
                    'source' => SibelStudent::SOURCE_SELF,
                ]);

                return $user;
            });
        } catch (QueryException $exception) {
            // Race langka (email/NIS duplikat bersamaan) — ubah jadi error validasi.
            if (($exception->errorInfo[0] ?? null) === '23000') {
                throw ValidationException::withMessages([
                    'email' => 'Email atau nomor induk tersebut sudah terdaftar.',
                ]);
            }

            throw $exception;
        }

        if (Auth::guest()) {
            // Anak-anak (tamu): login otomatis langsung ke halaman pengisian.
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()
                ->route('sibel.assessment.index')
                ->with('success', 'Pendaftaran berhasil. Silakan isi kuesioner gaya belajar.');
        }

        // Sudah login (mis. admin yang mengetes form): sesi TIDAK diganti —
        // cukup kembali ke form dengan kabar sukses akun baru sudah dibuat.
        return redirect()
            ->route('sibel.register')
            ->with('success', "Pendaftaran berhasil. Akun \"{$user->email}\" sudah dibuat — silakan login dengan akun tersebut untuk mulai mengisi.");
    }
}
