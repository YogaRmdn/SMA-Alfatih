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
        $validated = $request->validate([
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'maps_embed' => ['nullable', 'string', 'max:2000'],
            'operational_hours' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'string', 'max:50'],
            'longitude' => ['nullable', 'string', 'max:50'],
        ]);

        $contact->update($validated);

        return redirect()->route('admin.contact.edit')->with('success', 'Data kontak berhasil disimpan.');
    }
}
