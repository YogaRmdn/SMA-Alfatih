<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->with('role')
            ->when(request('q'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
            ->when(request('role_id'), fn ($q, $id) => $q->where('role_id', $id))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.form', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'email_verified_at' => now(),
        ]);

        $user->role_id = $request->role_id;
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.form', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user)
    {
        if ($this->roleChangeLocksOutEveryone($user, (int) $request->role_id)) {
            return back()->with('error', 'Tidak dapat menghapus atau menurunkan jabatan super admin terakhir.');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;

        if ($request->filled('password')) {
            $user->password = $request->password;
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($this->roleChangeLocksOutEveryone($user, null)) {
            return back()->with('error', 'Tidak dapat menghapus akun super admin terakhir.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Cegah penguncian total: jangan biarkan super admin terakhir
     * dihapus atau diturunkan jabatannya.
     */
    private function roleChangeLocksOutEveryone(User $user, ?int $newRoleId): bool
    {
        if (! $user->isSuperAdmin()) {
            return false;
        }

        $superAdminRoleId = Role::where('slug', 'super_admin')->value('id');

        if ($superAdminRoleId === $newRoleId) {
            return false;
        }

        $totalSuperAdmins = User::where('role_id', $superAdminRoleId)->count();

        return $totalSuperAdmins <= 1;
    }
}
