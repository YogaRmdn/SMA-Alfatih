<?php

namespace App\Services;

use App\Models\Ppdb;
use App\Models\PpdbFormField;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Support\Collection;

class PpdbFormDefinition
{
    /**
     * Tipe dokumen lama yang tetap bisa diakses walaupun sudah tidak ada di form.
     */
    public const LEGACY_DOCUMENT_TYPES = [
        'photo',
        'kk',
        'birth_certificate',
        'diploma',
        'report_card',
    ];

    public const PHOTO_DIRECTORY = 'ppdb/photos';

    public const DOCUMENT_DIRECTORY = 'ppdb/documents';

    private ?Collection $cache = null;

    public function fields(): Collection
    {
        return $this->cache ??= PpdbFormField::query()
            ->active()
            ->ordered()
            ->get();
    }

    public function fileFields(): Collection
    {
        return $this->fields()
            ->filter(fn (PpdbFormField $field) => $field->isFile())
            ->values();
    }

    public function fieldByKey(string $key): ?PpdbFormField
    {
        return $this->fields()->firstWhere('key', $key);
    }

    public function hasField(string $key): bool
    {
        return $this->fieldByKey($key) !== null;
    }

    /**
     * Kelompokkan field menjadi baris tampilan. Field yang berbagi
     * `group_label` digabung ke dalam satu pertanyaan, sama seperti
     * tampilan Google Form.
     *
     * @return array<int, array{heading: string, sub: bool, required: bool, fields: array<int, PpdbFormField>}>
     */
    public function rows(): array
    {
        $rows = [];
        $groupIndex = [];

        foreach ($this->fields() as $field) {
            $group = trim((string) $field->group_label);

            if ($group === '') {
                $rows[] = [
                    'heading' => $field->label,
                    'sub' => false,
                    'required' => (bool) $field->is_required,
                    'fields' => [$field],
                ];

                continue;
            }

            if (! isset($groupIndex[$group])) {
                $groupIndex[$group] = count($rows);
                $rows[] = [
                    'heading' => $group,
                    'sub' => true,
                    'required' => (bool) $field->is_required,
                    'fields' => [$field],
                ];

                continue;
            }

            $index = $groupIndex[$group];
            $rows[$index]['fields'][] = $field;
            $rows[$index]['required'] = $rows[$index]['required'] && (bool) $field->is_required;
        }

        return $rows;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = [];

        foreach ($this->fields() as $field) {
            $rules[$field->key] = $field->validationRules();
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = [];

        foreach ($this->fields() as $field) {
            $messages = [...$messages, ...$field->validationMessages()];
        }

        return $messages;
    }

    /**
     * @return array<int, string>
     */
    public function fileKeys(): array
    {
        return $this->fileFields()->pluck('key')->all();
    }

    /**
     * Tipe dokumen yang boleh diunggah admin saat verifikasi manual.
     *
     * @return array<int, string>
     */
    public function documentTypes(): array
    {
        return array_values(array_unique([
            ...$this->fileKeys(),
            'kk',
            'birth_certificate',
            'diploma',
            'report_card',
        ]));
    }

    /**
     * @return array<int, string>
     */
    public function allowedDocumentTypes(): array
    {
        return array_values(array_unique([
            ...self::LEGACY_DOCUMENT_TYPES,
            ...$this->fileKeys(),
        ]));
    }

    public function uploadDirectory(string $key): string
    {
        return $key === PpdbFormField::PHOTO_KEY
            ? self::PHOTO_DIRECTORY
            : self::DOCUMENT_DIRECTORY;
    }

    /**
     * Pisahkan input tervalidasi menjadi atribut kolom tabel `ppdb`,
     * kolom JSON `answers`, dan baris `ppdb_documents`.
     *
     * @param  array<string, mixed>  $values
     * @param  array<string, string>  $filePaths
     * @return array{columns: array<string, mixed>, answers: array<string, mixed>, documents: array<int, array<string, string>>}
     */
    public function persist(array $values, array $filePaths): array
    {
        $columns = [];
        $answers = [];
        $documents = [];

        foreach ($this->fields() as $field) {
            $key = $field->key;
            $path = $filePaths[$key] ?? null;

            if ($field->isFile()) {
                if ($key === PpdbFormField::PHOTO_KEY) {
                    if ($path) {
                        $columns['photo'] = $path;
                    }

                    continue;
                }

                if ($path) {
                    $documents[] = [
                        'type' => $key,
                        'name' => $field->label,
                        'file_path' => $path,
                    ];
                    $answers[$key] = $path;
                }

                continue;
            }

            if (! array_key_exists($key, $values)) {
                continue;
            }

            $value = $values[$key];
            $value = $value === '' ? null : $value;

            if (in_array($key, PpdbFormField::COLUMN_KEYS, true)) {
                $columns[$key] = $value;

                continue;
            }

            $answers[$key] = $value;
        }

        return [
            'columns' => $columns,
            'answers' => $answers,
            'documents' => $documents,
        ];
    }

    /**
     * Nilai satu field untuk ditampilkan di halaman detail pendaftar.
     */
    public function displayValue(Ppdb $ppdb, PpdbFormField $field): ?string
    {
        $value = $ppdb->answer($field->key);

        if ($value === null || $value === '') {
            return null;
        }

        if ($field->isFile()) {
            return is_scalar($value) ? (string) $value : null;
        }

        // Kolom tanggal di-cast Eloquent jadi objek carbon, bukan string.
        if ($value instanceof DateTimeInterface) {
            return $value instanceof DateTimeImmutable
                ? $value->format('d M Y')
                : $value->translatedFormat('d M Y');
        }

        if (! is_scalar($value)) {
            return null;
        }

        return $field->optionList()[$value] ?? (string) $value;
    }
}
