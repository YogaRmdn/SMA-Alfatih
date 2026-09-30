<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ppdb;
use App\Models\PpdbFormField;
use App\Services\PpdbFormDefinition;
use App\Traits\HasDeleteAll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PpdbController extends Controller
{
    use HasDeleteAll;

    public function deleteAll()
    {
        Ppdb::query()->chunkById(200, function ($registrations) {
            foreach ($registrations as $ppdb) {
                foreach ($ppdb->documents as $document) {
                    delete_file($document->file_path, 'local');
                    $document->delete();
                }

                delete_file($ppdb->photo, 'local');
                $ppdb->delete();
            }
        });

        return back()->with('success', 'Semua data pendaftar PPDB beserta dokumennya berhasil dihapus.');
    }

    public function index()
    {
        $registrations = Ppdb::query()
            ->with('documents')
            ->when(request('q'), fn ($q, $search) => $q->where(function ($sub) use ($search) {
                $sub->where('full_name', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('origin_school', 'like', "%{$search}%");
            }))
            ->when(request('status'), fn ($q, $status) => $q->where('status', $status))
            ->when(request('gender'), fn ($q, $gender) => $q->where('gender', $gender))
            ->when(request('program'), fn ($q, $program) => $q->where('program', $program))
            ->when(request('academic_year'), fn ($q, $year) => $q->where('academic_year', $year))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'pending' => Ppdb::where('status', 'pending')->count(),
            'verified' => Ppdb::where('status', 'verified')->count(),
            'lulus_administrasi' => Ppdb::where('status', 'lulus_administrasi')->count(),
            'rejected' => Ppdb::where('status', 'rejected')->count(),
            'accepted' => Ppdb::where('status', 'accepted')->count(),
        ];

        $programs = PpdbFormField::query()
            ->where('key', 'program')
            ->first()
            ?->optionList() ?? [];

        return view('admin.ppdb.index', compact('registrations', 'counts', 'programs'));
    }

    public function show(Ppdb $ppdb)
    {
        $ppdb->load('documents');

        $form = app(PpdbFormDefinition::class);

        $fields = $form->fields()
            ->filter(fn ($field) => ! $field->isFile() || $ppdb->hasDocument($field->key))
            ->values();

        // Dokumen yang boleh diisi panitia manual: empat dokumen wajib lama
        // plus berkas bertipe file yang aktif di formulir.
        $uploadable = collect($form->documentTypes())
            ->mapWithKeys(fn (string $type) => [$type => self::MANUAL_DOCUMENT_LABELS[$type] ?? Str::headline($type)])
            ->reject(fn (string $label, string $type) => $fields->contains(
                fn ($field) => $field->isFile() && $field->key === $type
            ));

        // Dokumen legacy yang dulu diunggah pendaftar, supaya tetap terlihat
        // meskipun tidak lagi ada di formulir.
        $legacyDocuments = $ppdb->documents
            ->reject(fn ($document) => in_array($document->type, $form->fileKeys(), true)
                || $document->type === 'other')
            ->values();

        return view('admin.ppdb.show', compact('ppdb', 'form', 'fields', 'uploadable', 'legacyDocuments'));
    }

    public function updateStatus(Request $request, Ppdb $ppdb)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'verified', 'lulus_administrasi', 'rejected', 'accepted'])],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $ppdb->update($validated);

        return back()->with('success', "Status pendaftaran {$ppdb->full_name} berhasil diubah menjadi {$ppdb->statusLabel()}.");
    }

    /**
     * Verifikasi manual: committee mengisi data identitas/dokumen yang tidak
     * lagi ditanyakan di formulir pendaftaran.
     */
    public function updateDetails(Request $request, Ppdb $ppdb)
    {
        $validated = $request->validate([
            'nisn' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'religion' => ['nullable', 'string', 'max:50'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'father_job' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'mother_job' => ['nullable', 'string', 'max:255'],
            'family_income' => ['nullable', 'string', 'max:255'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $ppdb->update($validated);

        return back()->with('success', 'Data verifikasi pendaftar berhasil disimpan.');
    }

    /**
     * Dokumen yang tidak lagi ditanyakan di formulir pendaftaran dan diisi
     * panitia secara manual saat verifikasi.
     */
    private const MANUAL_DOCUMENT_LABELS = [
        'kk' => 'Kartu Keluarga (KK)',
        'birth_certificate' => 'Akta Kelahiran',
        'diploma' => 'Ijazah / SKL',
        'report_card' => 'Rapor',
    ];

    /**
     * Unggah dokumen manual (KK, akta, ijazah, rapor) saat verifikasi.
     */
    public function storeDocument(Request $request, Ppdb $ppdb)
    {
        $allowed = app(PpdbFormDefinition::class)->documentTypes();

        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in($allowed)],
            'file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:'.PpdbFormField::DEFAULT_MAX_KB,
            ],
        ], [], [
            'type' => 'jenis dokumen',
            'file' => 'berkas',
        ]);

        // Unggah dulu, baru hapus berkas lama. Kalau `store()` gagal, dokumen
        // lama utuh dan tidak ada data yang hilang.
        $path = $validated['file']->store('ppdb/'.$ppdb->id, 'local');

        if ($path === false) {
            return back()->with('error', 'Gagal menyimpan berkas. Silakan coba lagi.');
        }

        $this->deleteDocumentFile($ppdb, $validated['type']);

        $ppdb->documents()->updateOrCreate(
            ['type' => $validated['type']],
            [
                'name' => self::MANUAL_DOCUMENT_LABELS[$validated['type']] ?? Str::headline($validated['type']),
                'file_path' => $path,
            ],
        );

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    public function destroyDocument(Ppdb $ppdb, string $type)
    {
        abort_unless(in_array($type, app(PpdbFormDefinition::class)->documentTypes(), true), 404);

        $this->deleteDocumentFile($ppdb, $type);

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function document(Ppdb $ppdb, string $type)
    {
        $allowed = array_merge(
            app(PpdbFormDefinition::class)->documentTypes(),
            [PpdbFormField::PHOTO_KEY],
        );

        abort_unless(in_array($type, $allowed, true), 404);

        $disk = Storage::disk('local');

        $path = $type === PpdbFormField::PHOTO_KEY
            ? $ppdb->photo
            : $ppdb->documents()->where('type', $type)->value('file_path');

        abort_unless($path && $disk->exists($path), 404);

        return $disk->response($path);
    }

    public function destroy(Ppdb $ppdb)
    {
        foreach ($ppdb->documents as $document) {
            delete_file($document->file_path, 'local');
            $document->delete();
        }

        delete_file($ppdb->photo, 'local');
        $ppdb->delete();

        return redirect()->route('admin.ppdb.index')->with('success', 'Data pendaftaran berhasil dihapus.');
    }

    /**
     * Hapus berkas lama sebelum menggantinya dengan unggahan verifikasi baru.
     */
    private function deleteDocumentFile(Ppdb $ppdb, string $type): void
    {
        $existing = $ppdb->documents()->where('type', $type)->first();

        if (! $existing) {
            return;
        }

        delete_file($existing->file_path, 'local');
        $existing->delete();
    }
}
