<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequest;
use App\Models\Staff;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = Staff::query()
            ->when(request('q'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.staffs.index', compact('staffs'));
    }

    public function create()
    {
        return view('admin.staffs.form');
    }

    public function store(StaffRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['photo'] = upload_file($request->file('photo'), 'staffs');

        Staff::create($data);

        return redirect()->route('admin.staffs.index')->with('success', 'Data staff berhasil ditambahkan.');
    }

    public function edit(Staff $staff)
    {
        return view('admin.staffs.form', compact('staff'));
    }

    public function update(StaffRequest $request, Staff $staff)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            delete_file($staff->photo);
            $data['photo'] = upload_file($request->file('photo'), 'staffs');
        } else {
            unset($data['photo']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $staff->update($data);

        return redirect()->route('admin.staffs.index')->with('success', 'Data staff berhasil diperbarui.');
    }

    public function destroy(Staff $staff)
    {
        delete_file($staff->photo);
        $staff->delete();

        return redirect()->route('admin.staffs.index')->with('success', 'Data staff berhasil dihapus.');
    }
}
