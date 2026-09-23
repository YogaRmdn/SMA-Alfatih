<?php

namespace App\Traits;

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

        $modelClass::query()->chunkById(200, function ($rows) use (&$deleted, $fileColumns) {
            foreach ($rows as $row) {
                foreach ($fileColumns as $column) {
                    delete_file($row->{$column} ?? null);
                }

                $row->forceDelete();
                $deleted++;
            }
        });

        return back()->with('success', "Semua data berhasil dihapus (total: {$deleted}).");
    }
}