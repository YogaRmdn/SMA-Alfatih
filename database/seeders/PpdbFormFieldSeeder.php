<?php

namespace Database\Seeders;

use App\Models\PpdbFormField;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PpdbFormFieldSeeder extends Seeder
{
    /**
     * Memuat konfigurasi formulir bawaan. Pakai insert-if-missing per key
     * supaya dijalankan ulang tidak menimpa kustomisasi admin.
     */
    public function run(): void
    {
        $now = now();

        foreach (PpdbFormField::defaults() as $index => $definition) {
            $sortOrder = $definition['sort_order'] ?? ($index + 1) * 10;

            unset($definition['sort_order']);

            DB::table('ppdb_form_fields')->insertOrIgnore([
                'group_label' => null,
                'width' => 'full',
                'placeholder' => null,
                'help_text' => null,
                'options' => null,
                'accept' => null,
                'max_kb' => null,
                ...$definition,
                'options' => isset($definition['options'])
                    ? json_encode($definition['options'], JSON_UNESCAPED_UNICODE)
                    : null,
                'sort_order' => $sortOrder,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
