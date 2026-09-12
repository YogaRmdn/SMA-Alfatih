<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function edit()
    {
        return view('admin.profile.change-password');
    }

    public function update(ChangePasswordRequest $request)
    {
        auth()->user()->update([
            'password' => Hash::make($request->validated('current_password')),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
