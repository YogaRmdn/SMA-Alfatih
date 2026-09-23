<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class SchoolProfileController extends Controller
{
    protected const SECTIONS = [
        'tentang-sejarah' => [
            'title' => 'Tentang & Sejarah',
            'card' => 'Tentang & Sejarah',
            'description' => 'Kelola halaman Tentang & Sejarah sekolah.',
            'route' => 'profile',
            'slug' => 'tentang-sejarah',
        ],
        'visi-misi' => [
            'title' => 'Visi Misi',
            'card' => 'Visi & Misi',
            'description' => 'Kelola halaman Visi & Misi sekolah.',
            'route' => 'visi',
            'slug' => 'visi-misi',
        ],
        'struktur-organisasi' => [
            'title' => 'Struktur Organisasi',
            'card' => 'Struktur Organisasi',
            'description' => 'Kelola halaman Struktur Organisasi sekolah.',
            'route' => 'structure',
            'slug' => 'struktur-organisasi',
        ],
        'sambutan' => [
            'title' => 'Sambutan Kepala Sekolah',
            'card' => 'Sambutan Kepala Sekolah',
            'description' => 'Kelola halaman Sambutan Kepala Sekolah.',
            'route' => 'welcome',
            'slug' => 'sambutan-kepala-sekolah',
        ],
    ];

    public function edit(string $section)
    {
        $meta = $this->section($section);
        $page = Page::query()->where('slug', $meta['slug'])->first();

        return view('admin.school-profile.form', [
            'meta' => $meta,
            'section' => $section,
            'page' => $page,
            'updateRoute' => 'admin.'.$meta['route'].'.update',
        ]);
    }

    public function update(Request $request, string $section)
    {
        $meta = $this->section($section);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Judul wajib diisi.',
        ]);

        $page = Page::query()->firstOrNew(['slug' => $meta['slug']]);
        $page->title = $validated['title'];
        $page->section = 'profile';
        $page->content = $validated['content'] ?? null;
        $page->is_active = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($page->image) {
                delete_file($page->image);
            }

            $page->image = upload_file($request->file('image'), 'pages');
        }

        $page->save();

        return redirect()->route('admin.'.$meta['route'].'.edit')
            ->with('success', 'Halaman '.$meta['card'].' berhasil disimpan.');
    }

    protected function section(string $section): array
    {
        abort_unless(isset(static::SECTIONS[$section]), 404);

        return static::SECTIONS[$section];
    }
}