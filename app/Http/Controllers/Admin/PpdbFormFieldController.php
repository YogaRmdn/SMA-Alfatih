<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PpdbFormFieldRequest;
use App\Models\PpdbFormField;
use App\Traits\HasDeleteAll;
use Database\Seeders\PpdbFormFieldSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PpdbFormFieldController extends Controller
{
    use HasDeleteAll;

    /**
     * Key inti yang tidak boleh dihapus karena dipakai oleh halaman detail
     * pendaftar dan ringkasan dokumen.
     *
     * @var array<int, string>
     */
    protected const PROTECTED_KEYS = [
        PpdbFormField::PHOTO_KEY,
        'full_name',
        'gender',
        'birth_date',
        'phone',
        'program',
        'info_source',
        'payment_proof',
    ];

    protected function deleteAllModel(): string
    {
        return PpdbFormField::class;
    }

    public function index(): View
    {
        $fields = PpdbFormField::query()->ordered()->get();

        return view('admin.ppdb-form-fields.index', compact('fields'));
    }

    public function create(): View
    {
        return view('admin.ppdb-form-fields.form');
    }

    public function store(PpdbFormFieldRequest $request): RedirectResponse
    {
        PpdbFormField::create($this->attributes($request));

        return redirect()
            ->route('admin.ppdb-form-fields.index')
            ->with('success', 'Pertanyaan formulir berhasil ditambahkan.');
    }

    public function edit(PpdbFormField $ppdbFormField): View
    {
        return view('admin.ppdb-form-fields.form', compact('ppdbFormField'));
    }

    public function update(PpdbFormFieldRequest $request, PpdbFormField $ppdbFormField): RedirectResponse
    {
        $data = $this->attributes($request);

        // Key dan tipe tidak boleh berubah karena sudah terikat ke lokasi
        // penyimpanan data pendaftar yang sudah terlanjur terkirim.
        unset($data['key'], $data['type']);

        $ppdbFormField->update($data);

        return redirect()
            ->route('admin.ppdb-form-fields.index')
            ->with('success', 'Pertanyaan formulir berhasil diperbarui.');
    }

    public function destroy(PpdbFormField $ppdbFormField): RedirectResponse
    {
        if (in_array($ppdbFormField->key, self::PROTECTED_KEYS, true)) {
            return back()->with('error', 'Pertanyaan inti tidak dapat dihapus. Nonaktifkan saja jika tidak ingin ditampilkan.');
        }

        $key = $ppdbFormField->key;
        $ppdbFormField->delete();

        return back()->with('success', "Pertanyaan '{$key}' berhasil dihapus dari formulir. Jawaban pendaftar lama tetap tersimpan.");
    }

    public function toggle(PpdbFormField $ppdbFormField): RedirectResponse
    {
        $ppdbFormField->update(['is_active' => ! $ppdbFormField->is_active]);

        return back()->with(
            'success',
            'Pertanyaan '.$ppdbFormField->label.($ppdbFormField->is_active ? ' ditampilkan.' : ' disembunyikan dari formulir.')
        );
    }

    public function reorder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:ppdb_form_fields,id'],
        ]);

        foreach (array_values($validated['order']) as $index => $id) {
            PpdbFormField::whereKey($id)->update(['sort_order' => ($index + 1) * 10]);
        }

        return back()->with('success', 'Urutan pertanyaan formulir berhasil disimpan.');
    }

    public function reset(): RedirectResponse
    {
        PpdbFormField::query()->delete();

        app(PpdbFormFieldSeeder::class)->run();

        return back()->with('success', 'Formulir dikembalikan ke konfigurasi awal.');
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(PpdbFormFieldRequest $request): array
    {
        $type = $request->string('type')->value();
        $hasOptions = in_array($type, ['select', 'radio'], true);
        $isFile = $type === 'file';

        return [
            'key' => $request->string('key')->value(),
            'label' => $request->string('label')->value(),
            'type' => $type,
            'group_label' => $request->filled('group_label') ? $request->string('group_label')->value() : null,
            'width' => $request->string('width')->value(),
            'placeholder' => $request->filled('placeholder') ? $request->string('placeholder')->value() : null,
            'help_text' => $request->filled('help_text') ? $request->string('help_text')->value() : null,
            'options' => $hasOptions ? ($request->parseOptions() ?: null) : null,
            'accept' => $isFile && $request->filled('accept') ? $request->string('accept')->value() : null,
            'max_kb' => $isFile ? (int) ($request->input('max_kb') ?: PpdbFormField::DEFAULT_MAX_KB) : null,
            'sort_order' => (int) ($request->input('sort_order') ?: 0),
            'is_required' => $request->boolean('is_required'),
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
