<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\PasienAsuransi;
use App\Models\PerusahaanAsuransi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasienAsuransiController extends Controller
{
    public function create(Pasien $pasien): View
    {
        $perusahaanAsuransis = PerusahaanAsuransi::tampil_asuransi();

        return view('petugas.pasien_asuransi.create', compact('pasien', 'perusahaanAsuransis'));
    }

    public function store(Request $request, Pasien $pasien): RedirectResponse
    {
        $validated = $request->validate([
            'perusahaan_asuransi_id' => ['required', 'exists:perusahaan_asuransis,id'],
            'no_polis' => ['required', 'string', 'max:100'],
            'prioritas' => ['required', 'integer', 'in:1,2'],
            'masa_berlaku' => ['required', 'date'],
        ]);

        $pasien->pasienAsuransis()->create($validated);

        return redirect()
            ->route('petugas.pasien.show', $pasien)
            ->with('success', 'Asuransi pasien berhasil didaftarkan.');
    }

    public function edit(Pasien $pasien, PasienAsuransi $asuransi): View
    {
        $perusahaanAsuransis = PerusahaanAsuransi::tampil_asuransi();

        return view('petugas.pasien_asuransi.edit', compact('pasien', 'asuransi', 'perusahaanAsuransis'));
    }

    public function update(Request $request, Pasien $pasien, PasienAsuransi $asuransi): RedirectResponse
    {
        $validated = $request->validate([
            'perusahaan_asuransi_id' => ['required', 'exists:perusahaan_asuransis,id'],
            'no_polis' => ['required', 'string', 'max:100'],
            'prioritas' => ['required', 'integer', 'in:1,2'],
            'masa_berlaku' => ['required', 'date'],
        ]);

        $asuransi->update($validated);

        return redirect()
            ->route('petugas.pasien.show', $pasien)
            ->with('success', 'Data asuransi pasien berhasil diperbarui.');
    }

    public function destroy(Pasien $pasien, PasienAsuransi $asuransi): RedirectResponse
    {
        if ($asuransi->klaimAsuransis()->exists()) {
            return back()->with('error', 'Asuransi tidak dapat dihapus karena sudah memiliki riwayat klaim.');
        }

        $asuransi->delete();

        return redirect()
            ->route('petugas.pasien.show', $pasien)
            ->with('success', 'Asuransi pasien berhasil dihapus.');
    }
}

