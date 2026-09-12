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
            'twitter' => ['nullable', 'url', 'max:500'],
            'maps_embed' => ['nullable', 'string', 'max:2000'],
            'operational_hours' => ['nullable', 'string', 'max:500'],
            'ppdb_open' => ['nullable', 'boolean'],
            'ppdb_tahun_ajaran' => ['nullable', 'string', 'max:50'],
            'meta_keywords' => ['nullable', 'string', 'max:1000'],
            'video_profile' => ['nullable', 'url', 'max:500'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = upload_file($request->file('logo'), 'settings');
        }
        if ($request->hasFile('favicon')) {
            $validated['favicon'] = upload_file($request->file('favicon'), 'settings');
        }

        $validated['ppdb_open'] = $request->boolean('ppdb_open') ? '1' : '0';

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        cache()->forget('site_settings');

        return redirect()->route('admin.settings.edit')->with('success', 'Pengaturan website berhasil disimpan.');
    }
}
