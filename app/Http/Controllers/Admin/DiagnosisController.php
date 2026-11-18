<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiagnosisController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $diagnoses = Diagnosis::query()
            ->when($search, function ($query, $search) {
                $query->where('nama_diagnosis', 'like', "%{$search}%")
                    ->orWhere('kode_diagnosis', 'like', "%{$search}%");
            })
            ->orderBy('kode_diagnosis')
            ->paginate(15)
            ->withQueryString();

        return view('admin.diagnosis.index', compact('diagnoses', 'search'));
    }

    public function create(): View
    {
        return view('admin.diagnosis.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_diagnosis' => ['required', 'string', 'max:50', 'unique:diagnoses,kode_diagnosis'],
            'nama_diagnosis' => ['required', 'string', 'max:255'],
        ]);

        Diagnosis::create($validated);

        return redirect()->route('admin.diagnosis.index')->with('success', 'Data diagnosis berhasil ditambahkan.');
    }

    public function edit(Diagnosis $diagnosis): View
    {
        return view('admin.diagnosis.edit', compact('diagnosis'));
    }

    public function update(Request $request, Diagnosis $diagnosis): RedirectResponse
    {
        $validated = $request->validate([
            'kode_diagnosis' => ['required', 'string', 'max:50', 'unique:diagnoses,kode_diagnosis,' . $diagnosis->id],
            'nama_diagnosis' => ['required', 'string', 'max:255'],
        ]);

        $diagnosis->update($validated);

        return redirect()->route('admin.diagnosis.index')->with('success', 'Data diagnosis berhasil diperbarui.');
    }

    public function destroy(Diagnosis $diagnosis): RedirectResponse
    {
        if ($diagnosis->kunjunganDiagnoses()->exists()) {
            return back()->with('error', 'Diagnosis tidak dapat dihapus karena sudah digunakan dalam riwayat pemeriksaan.');
        }

        $diagnosis->delete();

        return redirect()->route('admin.diagnosis.index')->with('success', 'Data diagnosis berhasil dihapus.');
    }
}

