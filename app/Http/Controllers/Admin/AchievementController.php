<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AchievementRequest;
use App\Models\Achievement;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::query()
            ->when(request('q'), fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when(request('category'), fn ($q, $category) => $q->where('category', $category))
            ->when(request('year'), fn ($q, $year) => $q->where('year', $year))
            ->orderBy('year', 'desc')
            ->orderBy('sort_order')
            ->paginate(10)
            ->withQueryString();

        return view('admin.achievements.index', compact('achievements'));
    }

    public function create()
    {
        return view('admin.achievements.form');
    }

    public function store(AchievementRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $data['photo'] = upload_file($request->file('photo'), 'achievements');

        Achievement::create($data);

        return redirect()->route('admin.achievements.index')->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function edit(Achievement $achievement)
    {
        return view('admin.achievements.form', compact('achievement'));
    }

    public function update(AchievementRequest $request, Achievement $achievement)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            delete_file($achievement->photo);
            $data['photo'] = upload_file($request->file('photo'), 'achievements');
        } else {
            unset($data['photo']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $achievement->update($data);

        return redirect()->route('admin.achievements.index')->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy(Achievement $achievement)
    {
        delete_file($achievement->photo);
        $achievement->delete();

        return redirect()->route('admin.achievements.index')->with('success', 'Prestasi berhasil dihapus.');
    }
}
