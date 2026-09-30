<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\PpdbRegistrationRequest;
use App\Models\Contact;
use App\Models\Ppdb;
use App\Models\PpdbDocument;
use App\Models\PpdbFormField;
use App\Models\Setting;
use App\Services\PpdbFormDefinition;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PpdbController extends Controller
{
    protected const PRIVATE_DISK = 'local';

    private function settings(): array
    {
        return Setting::query()->pluck('value', 'key')->toArray();
    }

    public function create()
    {
        $settings = $this->settings();
        $contact = Contact::first();

        if (($settings['ppdb_open'] ?? '0') !== '1') {
            return view('frontend.ppdb.closed', compact('settings', 'contact'));
        }

        $academicYear = $settings['ppdb_tahun_ajaran'] ?? (now()->year.'/'.(now()->year + 1));
        $form = app(PpdbFormDefinition::class);

        return view('frontend.ppdb.form', compact('settings', 'contact', 'academicYear', 'form'));
    }

    public function store(PpdbRegistrationRequest $request)
    {
        $settings = $this->settings();

        if (($settings['ppdb_open'] ?? '0') !== '1') {
            return redirect()->route('ppdb.register')->withErrors(['closed' => 'Pendaftaran PPDB sedang ditutup.']);
        }

        $form = app(PpdbFormDefinition::class);

        $data = $request->validated();
        $filePaths = [];

        foreach ($form->fileFields() as $field) {
            if ($request->hasFile($field->key)) {
                $filePaths[$field->key] = upload_file(
                    $request->file($field->key),
                    $form->uploadDirectory($field->key),
                    self::PRIVATE_DISK
                );
            }
        }

        $persist = $form->persist($data, $filePaths);

        $attributes = $persist['columns'];
        $attributes['academic_year'] = $settings['ppdb_tahun_ajaran'] ?? (now()->year.'/'.(now()->year + 1));
        $attributes['status'] = 'pending';
        $attributes['access_code'] = Ppdb::generateAccessCode();
        $attributes['answers'] = $persist['answers'];

        $ppdb = null;
        $exception = null;

        try {
            DB::beginTransaction();

            for ($attempt = 0; $attempt < 5; $attempt++) {
                $attributes['registration_number'] = Ppdb::generateRegistrationNumber();

                try {
                    $ppdb = Ppdb::query()->create($attributes);
                    break;
                } catch (QueryException $e) {
                    $exception = $e;

                    if (! $this->isRegistrationNumberCollision($e)) {
                        break;
                    }
                }
            }

            if ($ppdb) {
                foreach ($persist['documents'] as $document) {
                    PpdbDocument::create([
                        'ppdb_id' => $ppdb->id,
                        ...$document,
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            foreach ($filePaths as $path) {
                delete_file($path, self::PRIVATE_DISK);
            }

            throw $e;
        }

        if (! $ppdb) {
            foreach ($filePaths as $path) {
                delete_file($path, self::PRIVATE_DISK);
            }

            throw $exception
                ?? new QueryException('', [], new \RuntimeException('Pendaftaran gagal, silakan coba lagi.'));
        }

        session(['ppdb_registration_id' => $ppdb->id]);

        return redirect()->route('ppdb.success', $ppdb)->with('success', 'Pendaftaran berhasil dikirim.');
    }

    public function success(Ppdb $ppdb)
    {
        $contact = Contact::first();

        if (session('ppdb_registration_id') !== $ppdb->id) {
            return redirect()->route('ppdb.status')->withErrors([
                'registration_number' => 'Halaman ini hanya dapat diakses setelah menyelesaikan pendaftaran.',
            ]);
        }

        $form = app(PpdbFormDefinition::class);

        return view('frontend.ppdb.success', compact('ppdb', 'contact', 'form'));
    }

    public function status()
    {
        $settings = $this->settings();
        $contact = Contact::first();
        $form = app(PpdbFormDefinition::class);

        return view('frontend.ppdb.status', compact('settings', 'contact', 'form'));
    }

    public function checkStatus()
    {
        $validated = request()->validate([
            'registration_number' => ['required', 'string', 'max:50'],
            'birth_date' => ['required', 'date'],
            'access_code' => ['required', 'string', 'max:16'],
        ]);

        $ppdb = Ppdb::query()
            ->where('registration_number', $validated['registration_number'])
            ->whereDate('birth_date', $validated['birth_date'])
            ->where('access_code', strtoupper($validated['access_code']))
            ->first();

        $settings = $this->settings();
        $contact = Contact::first();
        $form = app(PpdbFormDefinition::class);

        if (! $ppdb) {
            return back()->withErrors([
                'registration_number' => 'No. registrasi, tanggal lahir, atau kode akses tidak cocok. Periksa kembali data Anda.',
            ])->withInput();
        }

        session(['ppdb_verified_id' => $ppdb->id]);

        return view('frontend.ppdb.status', compact('ppdb', 'settings', 'contact', 'form'));
    }

    public function document(Ppdb $ppdb, string $type)
    {
        abort_unless(in_array($type, app(PpdbFormDefinition::class)->allowedDocumentTypes(), true), 404);

        $authorized = session('ppdb_registration_id') === $ppdb->id
            || session('ppdb_verified_id') === $ppdb->id;

        abort_unless($authorized, 403);

        $disk = Storage::disk(self::PRIVATE_DISK);

        $path = $type === PpdbFormField::PHOTO_KEY
            ? $ppdb->photo
            : $ppdb->documents()->where('type', $type)->value('file_path');

        abort_unless($path && $disk->exists($path), 404);

        return $disk->response($path);
    }

    private function isRegistrationNumberCollision(QueryException $e): bool
    {
        $errorInfo = $e->errorInfo;

        // MySQL/MariaDB: SQLSTATE 23000 dengan driver error 1062.
        if (($errorInfo[1] ?? null) == 1062) {
            return true;
        }

        $message = is_array($errorInfo) ? (string) ($errorInfo[2] ?? '') : (string) $errorInfo;

        if (is_array($errorInfo) && str_contains($message, 'UNIQUE constraint failed')) {
            return true;
        }

        return str_contains($message, 'Duplicate entry')
            || str_contains($message, 'UNIQUE constraint failed');
    }
}
