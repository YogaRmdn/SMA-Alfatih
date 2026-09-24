<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings.edit');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:500'],
            'site_description' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'favicon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico', 'max:1024'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:500'],
            'facebook' => ['nullable', 'url', 'max:500'],
            'youtube' => ['nullable', 'url', 'max:500'],
            'tiktok' => ['nullable', 'url', 'max:500'],
            'maps_embed' => ['nullable', 'string', 'max:2000', $this->mapsEmbedRule()],
            'operational_hours' => ['nullable', 'string', 'max:500'],
            'ppdb_open' => ['nullable', 'boolean'],
            'ppdb_tahun_ajaran' => ['nullable', 'string', 'max:50'],
            'meta_keywords' => ['nullable', 'string', 'max:1000'],
            'video_profile' => ['nullable', 'url', 'max:500'],

            // Teks & judul halaman depan
            'hero_badge' => ['nullable', 'string', 'max:255'],
            'ticker_label' => ['nullable', 'string', 'max:255'],
            'profil_eyebrow' => ['nullable', 'string', 'max:255'],
            'profil_title_prefix' => ['nullable', 'string', 'max:255'],
            'profil_badge_1' => ['nullable', 'string', 'max:255'],
            'profil_badge_2' => ['nullable', 'string', 'max:255'],
            'profil_text' => ['nullable', 'string', 'max:2000'],
            'profil_photo_1' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'profil_photo_2' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'sambutan_eyebrow' => ['nullable', 'string', 'max:255'],
            'sambutan_title' => ['nullable', 'string', 'max:255'],
            'sambutan_intro' => ['nullable', 'string', 'max:255'],
            'sambutan_heading' => ['nullable', 'string', 'max:255'],
            'sambutan_text_1' => ['nullable', 'string', 'max:4000'],
            'sambutan_text_2' => ['nullable', 'string', 'max:4000'],
            'program_eyebrow' => ['nullable', 'string', 'max:255'],
            'program_title_prefix' => ['nullable', 'string', 'max:255'],
            'program_title_highlight' => ['nullable', 'string', 'max:255'],
            'program_subtitle' => ['nullable', 'string', 'max:2000'],
            'cta_title' => ['nullable', 'string', 'max:255'],
            'cta_text' => ['nullable', 'string', 'max:2000'],
            'fasilitas_eyebrow' => ['nullable', 'string', 'max:255'],
            'fasilitas_title' => ['nullable', 'string', 'max:255'],
            'fasilitas_subtitle' => ['nullable', 'string', 'max:2000'],
            'ekskul_eyebrow' => ['nullable', 'string', 'max:255'],
            'ekskul_title' => ['nullable', 'string', 'max:255'],
            'ekskul_subtitle' => ['nullable', 'string', 'max:2000'],
            'prestasi_eyebrow' => ['nullable', 'string', 'max:255'],
            'prestasi_title' => ['nullable', 'string', 'max:255'],
            'prestasi_subtitle' => ['nullable', 'string', 'max:2000'],
            'berita_eyebrow' => ['nullable', 'string', 'max:255'],
            'berita_title' => ['nullable', 'string', 'max:255'],
            'berita_badge' => ['nullable', 'string', 'max:255'],
            'galeri_eyebrow' => ['nullable', 'string', 'max:255'],
            'galeri_title' => ['nullable', 'string', 'max:255'],
            'galeri_subtitle' => ['nullable', 'string', 'max:2000'],
            'testimoni_eyebrow' => ['nullable', 'string', 'max:255'],
            'testimoni_title' => ['nullable', 'string', 'max:255'],
            'partner_title' => ['nullable', 'string', 'max:255'],
            'partner_subtitle' => ['nullable', 'string', 'max:2000'],
            'kontak_eyebrow' => ['nullable', 'string', 'max:255'],
            'kontak_title' => ['nullable', 'string', 'max:255'],
            'kontak_subtitle' => ['nullable', 'string', 'max:2000'],
            'stat_1_label' => ['nullable', 'string', 'max:255'],
            'stat_1_value' => ['nullable', 'string', 'max:50'],
            'stat_2_label' => ['nullable', 'string', 'max:255'],
            'stat_2_value' => ['nullable', 'string', 'max:50'],
            'stat_3_label' => ['nullable', 'string', 'max:255'],
            'stat_3_value' => ['nullable', 'string', 'max:50'],
        ]);

        if ($request->hasFile('logo')) {
            $this->deleteStoredFile('logo');
            $validated['logo'] = upload_file($request->file('logo'), 'settings');
        }
        if ($request->hasFile('favicon')) {
            $this->deleteStoredFile('favicon');
            $validated['favicon'] = upload_file($request->file('favicon'), 'settings');
        }
        foreach (['profil_photo_1', 'profil_photo_2'] as $photoKey) {
            if ($request->hasFile($photoKey)) {
                $this->deleteStoredFile($photoKey);
                $validated[$photoKey] = upload_file($request->file($photoKey), 'settings');
            }
        }

        $validated['ppdb_open'] = $request->boolean('ppdb_open') ? '1' : '0';

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        cache()->forget('site_settings');

        return redirect()->route('admin.settings.edit')->with('success', 'Pengaturan website berhasil disimpan.');
    }

    private function deleteStoredFile(string $key): void
    {
        $current = Setting::get($key);

        if (is_string($current) && $current !== '') {
            delete_file($current);
        }
    }

    /**
     * Rule validasi untuk URL embed peta (iframe Google Maps HTTPS).
     */
    private function mapsEmbedRule(): callable
    {
        return function ($attribute, $value, $fail) {
            if (empty($value)) {
                return;
            }

            $parts = parse_url($value);

            if (($parts['scheme'] ?? '') !== 'https' || ! isset($parts['host'])) {
                $fail('URL embed peta harus menggunakan HTTPS.');

                return;
            }

            $allowedHosts = [
                'maps.google.com',
                'www.google.com',
                'google.com',
                'maps.google.co.id',
                'www.google.co.id',
                'google.co.id',
            ];

            if (! in_array(strtolower($parts['host']), $allowedHosts, true)) {
                $fail('URL embed peta hanya diizinkan dari Google Maps.');

                return;
            }

            $path = $parts['path'] ?? '';
            $query = $parts['query'] ?? '';

            if (! str_contains($path, '/maps/') && ! str_contains($query, 'output=embed')) {
                $fail('URL embed peta tidak valid.');
            }
        };
    }
}
