<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasienController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $pasiens = Pasien::query()
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('no_identitas', 'like', "%{$search}%")
                    ->orWhere('no_telp', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('petugas.pasien.index', compact('pasiens', 'search'));
    }

    public function create(): View
    {
        return view('petugas.pasien.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'no_identitas' => ['required', 'string', 'max:50', 'unique:pasiens,no_identitas'],
            'nama' => ['required', 'string', 'max:255'],
            'tgl_lahir' => ['required', 'date'],
            'alamat' => ['nullable', 'string'],
            'no_telp' => ['nullable', 'string', 'max:20'],
        ]);

        $pasien = Pasien::simpan_pasien($validated);

        return redirect()
            ->route('petugas.pasien.show', $pasien)
            ->with('success', 'Data pasien berhasil ditambahkan.');
    }

    public function show(Pasien $pasien): View
    {
        $pasien->load(['pasienAsuransis.perusahaanAsuransi', 'kunjungans.dokter.user']);
        $riwayatKunjungan = $pasien->tampil_riwayat();

        return view('petugas.pasien.show', compact('pasien', 'riwayatKunjungan'));
    }

    public function edit(Pasien $pasien): View
    {
        return view('petugas.pasien.edit', compact('pasien'));
    }

    public function update(Request $request, Pasien $pasien): RedirectResponse
    {
        $validated = $request->validate([
            'no_identitas' => ['required', 'string', 'max:50', 'unique:pasiens,no_identitas,' . $pasien->id],
            'nama' => ['required', 'string', 'max:255'],
            'tgl_lahir' => ['required', 'date'],
            'alamat' => ['nullable', 'string'],
            'no_telp' => ['nullable', 'string', 'max:20'],
        ]);

        $pasien->update($validated);

        return redirect()
            ->route('petugas.pasien.show', $pasien)
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Pasien $pasien): RedirectResponse
    {
        if ($pasien->kunjungans()->exists()) {
            return back()->with('error', 'Pasien tidak dapat dihapus karena memiliki riwayat kunjungan.');
        }

        $pasien->delete();

        return redirect()
            ->route('petugas.pasien.index')
            ->with('success', 'Data pasien berhasil dihapus.');
    }
}

