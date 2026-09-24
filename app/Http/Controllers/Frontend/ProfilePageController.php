<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;

class ProfilePageController extends Controller
{
    protected const SECTIONS = [
        'tentang-sejarah' => [
            'label' => 'Tentang & Sejarah',
            'description' => 'Sejarah dan profil singkat SMA IT Tahfizh Al-Fatih sejak awal berdiri.',
        ],
        'visi-misi' => [
            'label' => 'Visi & Misi',
            'description' => 'Visi, misi, dan nilai yang menjadi arah penyelenggaraan pendidikan sekolah.',
        ],
        'struktur-organisasi' => [
            'label' => 'Struktur Organisasi',
            'description' => 'Bagan organisasi dan tata kelola sekolah.',
        ],
    ];

    public function tentang()
    {
        return $this->render('tentang-sejarah');
    }

    public function visiMisi()
    {
        return $this->render('visi-misi');
    }

    public function struktur()
    {
        return $this->render('struktur-organisasi');
    }

    private function render(string $slug)
    {
        $meta = static::SECTIONS[$slug];
        $page = Page::query()->where('slug', $slug)->first();

        abort_if($page && ! $page->is_active, 404);

        return view('frontend.profile.show', compact('meta', 'page'));
    }
}