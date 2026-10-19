<?php

namespace App\Policies;

use App\Models\SibelResult;
use App\Models\User;

class SibelResultPolicy
{
    /**
     * Manajemen hasil: admin dan guru BK.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin', 'admin', 'guru_bk');
    }

    /**
     * Siswa hanya boleh melihat hasilnya sendiri dan hanya jika sudah
     * dipublikasikan oleh guru BK.
     */
    public function view(User $user, SibelResult $result): bool
    {
        if ($this->viewAny($user)) {
            return true;
        }

        if (! $user->hasRole('siswa')) {
            return false;
        }

        return $result->attempt->student->user_id === $user->id
            && $result->is_published;
    }

    /**
     * Hanya admin/guru BK yang boleh mengubah catatan & publikasi hasil.
     */
    public function update(User $user, SibelResult $result): bool
    {
        return $this->viewAny($user);
    }
}
