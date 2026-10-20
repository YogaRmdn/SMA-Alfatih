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
     * Hasil hanya untuk pengelola — sisi siswa mengakses hasil miliknya
     * lewat session terikat (PortalController), bukan akun login.
     */
    public function view(User $user, SibelResult $result): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Hanya admin/guru BK yang boleh mengubah catatan & publikasi hasil.
     */
    public function update(User $user, SibelResult $result): bool
    {
        return $this->viewAny($user);
    }
}
