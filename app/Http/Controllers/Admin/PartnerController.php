<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartnerRequest;
use App\Models\Partner;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::query()
            ->when(request('q'), fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partners.form');
    }

    public function store(PartnerRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['logo'] = upload_file($request->file('logo'), 'partners');

        Partner::create($data);

        return redirect()->route('admin.partners.index')->with('success', 'Partner berhasil ditambahkan.');
    }

    public function edit(Partner $partner)
    {
        return view('admin.partners.form', compact('partner'));
    }

    public function update(PartnerRequest $request, Partner $partner)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            delete_file($partner->logo);
            $data['logo'] = upload_file($request->file('logo'), 'partners');
        } else {
            unset($data['logo']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $partner->update($data);

        return redirect()->route('admin.partners.index')->with('success', 'Partner berhasil diperbarui.');
    }

    public function destroy(Partner $partner)
    {
        delete_file($partner->logo);
        $partner->delete();

        return redirect()->route('admin.partners.index')->with('success', 'Partner berhasil dihapus.');
    }
}
