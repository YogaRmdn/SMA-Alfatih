<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\PpdbRegistrationRequest;
use App\Models\Contact;
use App\Models\Ppdb;
use App\Models\PpdbDocument;
use App\Models\Setting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PpdbController extends Controller
{
    protected const ALLOWED_DOCUMENT_TYPES = [
        'photo',
        'kk',
        'birth_certificate',
        'diploma',
        'report_card',
    ];

    protected const DOCUMENT_LABELS = [
        'kk' => 'Kartu Keluarga (KK)',
        'birth_certificate' => 'Akta Kelahiran',
        'diploma' => 'Ijazah / SKL',
        'report_card' => 'Rapor',
    ];

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

        return view('frontend.ppdb.form', compact('settings', 'contact', 'academicYear'));
    }

    public function store(PpdbRegistrationRequest $request)
    {
        $settings = $this->settings();

        if (($settings['ppdb_open'] ?? '0') !== '1') {
            return redirect()->route('ppdb.register')->withErrors(['closed' => 'Pendaftaran PPDB sedang ditutup.']);
        }

        $data = $request->validated();

        $data['gender'] = $request->gender;
        $data['religion'] = $request->filled('religion') ? $request->religion : 'Islam';
        $data['academic_year'] = $settings['ppdb_tahun_ajaran'] ?? (now()->year.'/'.(now()->year + 1));
        $data['status'] = 'pending';
        $data['access_code'] = Ppdb::generateAccessCode();
        $data['photo'] = upload_file($request->file('photo'), 'ppdb/photos', self::PRIVATE_DISK);

        $documentPaths = [];

        foreach (array_keys(self::DOCUMENT_LABELS) as $type) {
            if ($request->hasFile($type)) {
                $documentPaths[$type] = upload_file($request->file($type), 'ppdb/documents', self::PRIVATE_DISK);
            }
        }

        $ppdb = null;
        $exception = null;

        DB::beginTransaction();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $data['registration_number'] = Ppdb::generateRegistrationNumber();

            try {
                $ppdb = Ppdb::query()->create($data);
                break;
            } catch (QueryException $e) {
                $exception = $e;

                if (! $this->isRegistrationNumberCollision($e)) {
                    break;
                }
            }
        }

        if (! $ppdb) {
            DB::rollBack();
            delete_file($data['photo'], self::PRIVATE_DISK);
            foreach ($documentPaths as $path) {
                delete_file($path, self::PRIVATE_DISK);
            }

            throw $exception
                ?? new QueryException('', [], new \RuntimeException('Pendaftaran gagal, silakan coba lagi.'));
        }

        foreach ($documentPaths as $type => $path) {
            PpdbDocument::create([
                'ppdb_id' => $ppdb->id,
                'name' => self::DOCUMENT_LABELS[$type],
                'type' => $type,
                'file_path' => $path,
            ]);
        }

        DB::commit();

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

        return view('frontend.ppdb.success', compact('ppdb', 'contact'));
    }

    public function status()
    {
        $settings = $this->settings();
        $contact = Contact::first();

        return view('frontend.ppdb.status', compact('settings', 'contact'));
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

        if (! $ppdb) {
            return back()->withErrors([
                'registration_number' => 'No. registrasi, tanggal lahir, atau kode akses tidak cocok. Periksa kembali data Anda.',
            ])->withInput();
        }

        session(['ppdb_verified_id' => $ppdb->id]);

        return view('frontend.ppdb.status', compact('ppdb', 'settings', 'contact'));
    }

    public function document(Ppdb $ppdb, string $type)
    {
        abort_unless(in_array($type, self::ALLOWED_DOCUMENT_TYPES, true), 404);

        $authorized = session('ppdb_registration_id') === $ppdb->id
            || session('ppdb_verified_id') === $ppdb->id;

        abort_unless($authorized, 403);

        $disk = Storage::disk(self::PRIVATE_DISK);

        $path = $type === 'photo'
            ? $ppdb->photo
            : $ppdb->documents()->where('type', $type)->value('file_path');

        abort_unless($path && $disk->exists($path), 404);

        return $disk->response($path);
    }

    private function isRegistrationNumberCollision(QueryException $e): bool
    {
        $errorInfo = $e->errorInfo;
        $code = $errorInfo[1] ?? null;

        if (is_string($errorInfo) && str_contains($errorInfo, 'UNIQUE constraint failed')) {
            return true;
        }

        return $code == 1062 || str_contains((string) $e->getMessage(), 'unique');
    }
}
