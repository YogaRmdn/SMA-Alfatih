<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ppdb;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PpdbController extends Controller
{
    public function index()
    {
        $registrations = Ppdb::query()
            ->with('documents')
            ->when(request('q'), fn ($q, $search) => $q->where('full_name', 'like', "%{$search}%")
                ->orWhere('registration_number', 'like', "%{$search}%")
                ->orWhere('nisn', 'like', "%{$search}%"))
            ->when(request('status'), fn ($q, $status) => $q->where('status', $status))
            ->when(request('gender'), fn ($q, $gender) => $q->where('gender', $gender))
            ->when(request('academic_year'), fn ($q, $year) => $q->where('academic_year', $year))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'pending' => Ppdb::where('status', 'pending')->count(),
            'verified' => Ppdb::where('status', 'verified')->count(),
            'lulus_administrasi' => Ppdb::where('status', 'lulus_administrasi')->count(),
            'rejected' => Ppdb::where('status', 'rejected')->count(),
            'accepted' => Ppdb::where('status', 'accepted')->count(),
        ];

        return view('admin.ppdb.index', compact('registrations', 'counts'));
    }

    public function show(Ppdb $ppdb)
    {
        $ppdb->load('documents');

        return view('admin.ppdb.show', compact('ppdb'));
    }

    public function updateStatus(Request $request, Ppdb $ppdb)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'verified', 'lulus_administrasi', 'rejected', 'accepted'])],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $ppdb->update($validated);

        return back()->with('success', "Status pendaftaran {$ppdb->full_name} berhasil diubah menjadi {$ppdb->statusLabel()}.");
    }

    public function destroy(Ppdb $ppdb)
    {
        foreach ($ppdb->documents as $document) {
            delete_file($document->file_path);
            $document->delete();
        }

        delete_file($ppdb->photo);
        $ppdb->delete();

        return redirect()->route('admin.ppdb.index')->with('success', 'Data pendaftaran berhasil dihapus.');
    }
}
