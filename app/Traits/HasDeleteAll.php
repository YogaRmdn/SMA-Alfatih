<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

trait HasDeleteAll
{
    /**
     * Nama class model yang datanya akan dihapus massal.
     */
    protected function deleteAllModel(): string
    {
        throw new \LogicException('Model untuk penghapusan massal belum ditentukan.');
    }

    /**
     * Kolom file (atribut model) di disk public yang ikut dibersihkan.
     *
     * @return array<int, string>
     */
    protected function deleteAllFileColumns(): array
    {
        return [];
    }

    public function deleteAll()
    {
        $modelClass = $this->deleteAllModel();
        $fileColumns = $this->deleteAllFileColumns();
        $deleted = 0;

        $query = $modelClass::query();

        if (in_array(SoftDeletes::class, class_uses_recursive($modelClass), true)) {
            $query->withTrashed();
        }

        $query->chunkById(200, function ($rows) use (&$deleted, $fileColumns) {
            foreach ($rows as $row) {
                if (in_array(SoftDeletes::class, class_uses_recursive($row), true)) {
                    $row->forceDelete();
                } else {
                    $row->delete();
                }

                foreach ($fileColumns as $column) {
                    delete_file($row->{$column} ?? null);
                }

                $deleted++;
            }
        });

        return back()->with('success', "Semua data berhasil dihapus (total: {$deleted}).");
    }
}