<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function edit()
    {
        $contact = Contact::query()->firstOrCreate(['id' => 1]);

        return view('admin.contact.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $contact = Contact::query()->firstOrCreate(['id' => 1]);

        $validated = $request->validate([
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'maps_embed' => ['nullable', 'string', 'max:2000', function ($attribute, $value, $fail) {
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
            }],
            'operational_hours' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'string', 'max:50'],
            'longitude' => ['nullable', 'string', 'max:50'],
        ]);

        $contact->update($validated);

        return redirect()->route('admin.contact.edit')->with('success', 'Data kontak berhasil disimpan.');
    }
}
